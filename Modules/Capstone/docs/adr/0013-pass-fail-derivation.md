# Derive PASS/FAIL from weighted average (threshold 60)

Examiner `result` was a free manual input (`PASS`/`FAIL`) independent of indicator scores, so a 40 average could be submitted as `PASS`. We derive `PASS/FAIL` from the weighted average in `SchedulingService::deriveResult()` (`>=60 PASS`) and reject mismatched payloads with 422.

## Considered Options

- **Manual result (status quo)**: trusts the examiner, but allows score/result divergence with no audit signal.
- **Per-indicator minimum (every indicator >=60)**: stricter and matches “minimal 60” literally, but fails students with one weak indicator despite a strong average; rejected per locked product decision (average-based).
- **Derived average (chosen)**: single source of truth, backward compatible with stored rows (completed schedules keep their locked result), supervisor evaluations expose `derived_result` as read-model only without a new write column.
