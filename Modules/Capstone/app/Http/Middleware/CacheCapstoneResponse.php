<?php

namespace Modules\Capstone\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Short-lived, private response cache for authenticated Capstone JSON APIs.
 *
 * The cache is scoped per user, active role, URL, query string, and a global
 * version. Every successful mutation advances the version so subsequent reads
 * cannot reuse data from before the mutation. This works with Redis, file, and
 * database cache stores and deliberately fails open if the cache is unavailable.
 */
class CacheCapstoneResponse
{
    private const VERSION_KEY = 'capstone:http-cache:version';

    public function handle(Request $request, Closure $next): Response
    {
        if (! (bool) config('capstone.http_cache.enabled', true)) {
            return $next($request);
        }

        if (! $request->isMethod('GET')) {
            $response = $next($request);

            if ($response->isSuccessful()) {
                $this->invalidate();
            }

            return $this->withQueryCacheDiagnostics($request, $response);
        }

        if ($this->shouldBypass($request)) {
            $response = $next($request);
            $response->headers->set('X-Capstone-Cache', 'BYPASS');

            return $this->withQueryCacheDiagnostics($request, $response);
        }

        try {
            $version = Cache::rememberForever(self::VERSION_KEY, static fn (): int => 1);
            $key = $this->key($request, $version);
            $cached = Cache::get($key);

            if (is_array($cached)) {
                return $this->cachedResponse($request, $cached);
            }
        } catch (Throwable) {
            // Cache downtime must never make the API unavailable.
            $key = null;
        }

        $response = $next($request);

        if (! $response instanceof JsonResponse || ! $response->isSuccessful()) {
            return $this->withQueryCacheDiagnostics($request, $response);
        }

        $content = (string) $response->getContent();
        $etag = '"'.sha1($content).'"';
        $response->setEtag(trim($etag, '"'));
        $response->headers->set('Cache-Control', 'private, max-age=0, must-revalidate');
        $response->headers->set('X-Capstone-Cache', 'MISS');

        $maxBytes = max(0, (int) config('capstone.http_cache.max_bytes', 1_048_576));
        if ($key !== null && strlen($content) <= $maxBytes) {
            $payload = [
                'content' => $content,
                'status' => $response->getStatusCode(),
                'content_type' => $response->headers->get('Content-Type', 'application/json'),
                'etag' => $etag,
            ];

            try {
                Cache::put(
                    $key,
                    $payload,
                    max(1, (int) config('capstone.http_cache.ttl_seconds', 30))
                );
            } catch (Throwable) {
                // The uncached response is still valid.
            }
        }

        return $this->withQueryCacheDiagnostics($request, $response);
    }

    private function shouldBypass(Request $request): bool
    {
        $cacheControl = strtolower((string) $request->header('Cache-Control'));

        return $request->boolean('_fresh')
            || $request->headers->has('Range')
            || str_contains($cacheControl, 'no-store')
            || str_contains($cacheControl, 'no-cache');
    }

    private function key(Request $request, int|string $version): string
    {
        $identity = implode('|', [
            $version,
            (string) ($request->user()?->getAuthIdentifier() ?? 'guest'),
            (string) ($request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role', 'default')),
            $request->getPathInfo(),
            (string) $request->getQueryString(),
            (string) $request->header('Accept-Language', 'id'),
        ]);

        return 'capstone:http-cache:response:'.sha1($identity);
    }

    /** @param array{content: string, status: int, content_type: string, etag: string} $cached */
    private function cachedResponse(Request $request, array $cached): Response
    {
        $headers = [
            'Content-Type' => $cached['content_type'],
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'ETag' => $cached['etag'],
            'X-Capstone-Cache' => 'HIT',
            'X-DB-Query-Cache' => $this->queryCacheDiagnostics($request),
        ];

        if ($request->headers->get('If-None-Match') === $cached['etag']) {
            return response('', Response::HTTP_NOT_MODIFIED, $headers);
        }

        return response($cached['content'], $cached['status'], $headers);
    }

    private function invalidate(): void
    {
        try {
            Cache::add(self::VERSION_KEY, 1, now()->addYears(10));
            Cache::increment(self::VERSION_KEY);
        } catch (Throwable) {
            // Cache downtime must not turn a successful mutation into an error.
        }
    }

    private function withQueryCacheDiagnostics(Request $request, Response $response): Response
    {
        $response->headers->set('X-DB-Query-Cache', $this->queryCacheDiagnostics($request));

        return $response;
    }

    private function queryCacheDiagnostics(Request $request): string
    {
        $hits = (int) $request->attributes->get('db_query_cache_hits', 0);
        $misses = (int) $request->attributes->get('db_query_cache_misses', 0);

        return "hits={$hits}; misses={$misses}";
    }
}
