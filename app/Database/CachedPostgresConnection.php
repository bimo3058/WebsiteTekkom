<?php

namespace App\Database;

use DateTimeInterface;
use Illuminate\Cache\Repository;
use Illuminate\Database\PostgresConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * PostgreSQL connection with a short-lived Redis cache for safe read queries.
 *
 * Only SELECT queries from HTTP GET/HEAD requests are cached. Transactions,
 * locking reads, volatile SQL functions, console jobs, and write-returning SQL
 * always hit PostgreSQL. Writes advance a global generation after commit,
 * making previously cached results unreachable. Transactional writes are batched.
 */
class CachedPostgresConnection extends PostgresConnection
{
    private const VERSION_KEY = 'database:query-cache:version';

    private bool $pendingCacheInvalidation = false;

    private ?string $timeoutFingerprint = null;

    public function select($query, $bindings = [], $useReadPdo = true)
    {
        if ($this->isWriteReturningQuery($query)) {
            $result = parent::select($query, $bindings, $useReadPdo);
            $this->invalidateQueryCache();

            return $result;
        }

        if (! $this->shouldCache($query)) {
            return parent::select($query, $bindings, $useReadPdo);
        }

        $store = $this->cacheStore();
        if (! $store) {
            return parent::select($query, $bindings, $useReadPdo);
        }

        try {
            $version = $this->cacheVersion($store);
            $key = $this->cacheKey($query, $bindings, $version);
            $cached = $store->get($key);

            if (is_array($cached) && array_key_exists('rows', $cached)) {
                $this->recordCacheResult('hit');

                return $cached['rows'];
            }
        } catch (Throwable) {
            $this->markCacheUnavailable();

            return parent::select($query, $bindings, $useReadPdo);
        }

        $this->ensureTimeouts();

        $rows = parent::select($query, $bindings, $useReadPdo);
        $this->recordCacheResult('miss');

        try {
            $maxBytes = max(0, (int) config('database.query_cache.max_bytes', 2_097_152));
            if ($maxBytes > 0 && strlen(serialize($rows)) <= $maxBytes) {
                $store->put(
                    $key,
                    ['rows' => $rows],
                    max(1, (int) config('database.query_cache.ttl_seconds', 60))
                );
            }
        } catch (Throwable) {
            // A valid database response must survive Redis/cache failures.
            $this->markCacheUnavailable();
        }

        return $rows;
    }

    public function statement($query, $bindings = [])
    {
        $this->ensureTimeouts();

        $result = parent::statement($query, $bindings);

        if ($result && ! $this->pretending()) {
            $this->invalidateQueryCache();
        }

        return $result;
    }

    public function affectingStatement($query, $bindings = [])
    {
        $this->ensureTimeouts();

        $affected = parent::affectingStatement($query, $bindings);

        if ($affected > 0 && ! $this->pretending()) {
            $this->invalidateQueryCache();
        }

        return $affected;
    }

    public function unprepared($query)
    {
        $this->ensureTimeouts();

        $result = parent::unprepared($query);

        if ($result && ! $this->pretending()) {
            $this->invalidateQueryCache();
        }

        return $result;
    }

    public function prepareBindings(array $bindings)
    {
        $grammar = $this->getQueryGrammar();

        foreach ($bindings as $key => $value) {
            if ($value instanceof DateTimeInterface) {
                $bindings[$key] = $value->format($grammar->getDateFormat());
            } elseif (is_bool($value)) {
                // Preserve the PostgreSQL boolean handling previously registered
                // by the ManajemenMahasiswa module connection resolver.
                $bindings[$key] = $value ? 'true' : 'false';
            }
        }

        return $bindings;
    }

    public function invalidateQueryCache(): void
    {
        if ($this->transactionLevel() > 0) {
            $this->pendingCacheInvalidation = true;

            return;
        }

        $store = $this->cacheStore();
        if (! $store) {
            return;
        }

        try {
            $store->add(self::VERSION_KEY, 1, now()->addYears(10));
            $version = $store->increment(self::VERSION_KEY);

            if (app()->bound('request') && app('request') instanceof Request) {
                app('request')->attributes->set('db_query_cache_version', $version);
            }
        } catch (Throwable) {
            // Database writes must never fail because Redis is unavailable.
            $this->markCacheUnavailable();
        }
    }

    protected function fireConnectionEvent($event)
    {
        // Laravel's transaction() commits PDO directly, so handle both that
        // path and explicit commit() here. Nested commits must not publish yet.
        if ($this->transactionLevel() === 0 && in_array($event, ['committed', 'rollingBack'], true)) {
            $invalidate = $event === 'committed' && $this->pendingCacheInvalidation;
            $this->pendingCacheInvalidation = false;

            if ($invalidate) {
                $this->invalidateQueryCache();
            }
        }

        return parent::fireConnectionEvent($event);
    }

    /**
     * Enforce fail-fast Postgres timeouts so a stalled pooler/lock surfaces
     * as a catchable QueryException instead of a PHP max-execution fatal.
     */
    private function ensureTimeouts(): void
    {
        if ($this->pretending()) {
            return;
        }

        if ($this->getDriverName() !== 'pgsql') {
            return;
        }

        try {
            $pdo = $this->getPdo();
        } catch (Throwable) {
            return;
        }

        $fingerprint = spl_object_hash($pdo);
        if ($this->timeoutFingerprint === $fingerprint) {
            return;
        }

        try {
            $readPdo = $this->getReadPdo();
            if ($readPdo !== $pdo) {
                $readPdo->exec("SET statement_timeout = '{$this->timeoutSetting('statement_timeout', '5s')}'");
                $readPdo->exec("SET lock_timeout = '{$this->timeoutSetting('lock_timeout', '5s')}'");
            }
        } catch (Throwable) {
            // Read PDO is optional; write PDO enforcement below is what matters.
        }

        try {
            $pdo->exec("SET statement_timeout = '{$this->timeoutSetting('statement_timeout', '5s')}'");
            $pdo->exec("SET lock_timeout = '{$this->timeoutSetting('lock_timeout', '5s')}'");
            $pdo->exec("SET idle_in_transaction_session_timeout = '{$this->timeoutSetting('idle_in_transaction_session_timeout', '10s')}'");

            $this->timeoutFingerprint = $fingerprint;
        } catch (Throwable) {
            // Fail-open: the original query must still run. PHP's
            // max_execution_time remains as the backstop.
        }
    }

    private function timeoutSetting(string $key, string $default): string
    {
        $value = $this->getConfig($key);
        if ($value === null) {
            $value = config("database.connections.{$this->getName()}.{$key}", $default);
        }

        $value = trim((string) $value);
        if (! preg_match('/^\d+(ms|s|min|h)?$/', $value)) {
            return $default;
        }

        return str_replace("'", '', $value);
    }

    private function shouldCache(string $query): bool
    {
        if (! (bool) config('database.query_cache.enabled', true)
            || $this->transactionLevel() > 0
            || ! $this->isCacheableRequest()) {
            return false;
        }

        $sql = strtolower(preg_replace('/\s+/', ' ', trim($query)) ?? trim($query));
        if (! preg_match('/^(select|with)\b/', $sql)) {
            return false;
        }

        $unsafeFragments = [
            ' for update',
            ' for share',
            ' skip locked',
            ' now(',
            ' current_timestamp',
            ' current_date',
            ' current_time',
            ' clock_timestamp(',
            ' statement_timestamp(',
            ' transaction_timestamp(',
            ' random(',
            ' nextval(',
            ' currval(',
            ' setval(',
            ' lastval(',
            ' pg_advisory',
            ' pg_catalog.',
            ' information_schema.',
        ];

        foreach ($unsafeFragments as $fragment) {
            if (str_contains($sql, $fragment)) {
                return false;
            }
        }

        return true;
    }

    private function isCacheableRequest(): bool
    {
        if ((bool) config('database.query_cache.cache_console', false)) {
            return true;
        }

        if (! app()->bound('request')) {
            return false;
        }

        $request = app('request');
        if (! $request instanceof Request || ! $request->server('REQUEST_METHOD')) {
            return false;
        }

        return in_array($request->method(), ['GET', 'HEAD'], true);
    }

    private function isWriteReturningQuery(string $query): bool
    {
        $sql = strtolower(ltrim($query));

        return (bool) preg_match('/^(insert|update|delete)\b|^with\b[\s\S]*\b(insert|update|delete)\b/', $sql);
    }

    private function cacheKey(string $query, array $bindings, int|string $version): string
    {
        $identity = [
            'version' => $version,
            'connection' => $this->getName(),
            'database' => $this->getDatabaseName(),
            'query' => $query,
            'bindings' => $this->prepareBindings($bindings),
        ];

        return 'database:query-cache:result:'.hash('sha256', serialize($identity));
    }

    private function cacheVersion(Repository $store): int|string
    {
        $request = app()->bound('request') ? app('request') : null;
        if ($request instanceof Request && $request->attributes->has('db_query_cache_version')) {
            return $request->attributes->get('db_query_cache_version');
        }

        $version = $store->rememberForever(self::VERSION_KEY, static fn (): int => 1);

        if ($request instanceof Request) {
            $request->attributes->set('db_query_cache_version', $version);
        }

        return $version;
    }

    private function cacheStore(): ?Repository
    {
        if (! (bool) config('database.query_cache.enabled', true)) {
            return null;
        }

        $storeName = (string) config('database.query_cache.store', 'redis');

        $request = app()->bound('request') ? app('request') : null;
        if ($request instanceof Request && $request->attributes->get('db_query_cache_unavailable')) {
            return null;
        }

        // A database-backed cache would recursively query this connection.
        if ($storeName === 'database') {
            return null;
        }

        try {
            return Cache::store($storeName);
        } catch (Throwable) {
            $this->markCacheUnavailable();

            return null;
        }
    }

    private function markCacheUnavailable(): void
    {
        $request = app()->bound('request') ? app('request') : null;
        if ($request instanceof Request) {
            // Retry next request, instead of paying a Redis timeout per query.
            $request->attributes->set('db_query_cache_unavailable', true);
        }
    }

    private function recordCacheResult(string $result): void
    {
        if (! app()->bound('request')) {
            return;
        }

        $request = app('request');
        if (! $request instanceof Request) {
            return;
        }

        $attribute = $result === 'hit'
            ? 'db_query_cache_hits'
            : 'db_query_cache_misses';
        $request->attributes->set(
            $attribute,
            (int) $request->attributes->get($attribute, 0) + 1
        );
    }
}
