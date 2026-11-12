# Fail-fast Supabase pooler writes

Enforce ~5s Postgres `statement_timeout`/`lock_timeout` (10s `idle_in_transaction`) on the pgsql connection via `CachedPostgresConnection`, keeping `prepare=>false` for the `:6543` pooler. A stalled `UPDATE users` therefore raises a catchable timeout that presence swallows instead of hitting PHP `max_execution_time` at `Connection::affectingStatement()`.

## Considered Options

- Raise `max_execution_time`: keeps workers blocked on `php artisan serve` single-threaded dev server and hides pooler saturation.
- Queue every heartbeat: heavier infra for a stale-tolerant flag; deferred until latency proves necessary.
