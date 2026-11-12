# Presence is best-effort, reconciled by a reaper

Heartbeat (`is_online/last_seen_at`) is throttled to once per 120s per user and reconciled by the 1-minute `app:set-users-offline` reaper (5-minute staleness). UI must treat `is_online` as stale-tolerant; presence writes never throw past `terminate()`.
