# Presence

Best-effort online tracking for authenticated users, reconciled by a reaper. Presence may be stale up to 5 minutes and must never block a response.

## Language

**Presence**:
Throttled `is_online/last_seen_at` heartbeat, at most once per 120 seconds per user.
_Avoid_: Real-time online, session

**Heartbeat**:
`UserPresence::touch()` write issued after the response via terminable middleware.
_Avoid_: Activity log, audit

**Reaper**:
`app:set-users-offline` cron flipping `is_online=false` when heartbeat is stale beyond 5 minutes.
_Avoid_: Logout

**Fail-fast write**:
Postgres `statement_timeout/lock_timeout` (~5s) turning a stalled pooler or lock into a catchable timeout instead of a PHP fatal.
_Avoid_: max_execution_time increase
