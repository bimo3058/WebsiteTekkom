# Capstone Refactor — Remaining Work Plan

> Handoff doc. Scope: `Modules/Capstone/` only. No commits without approval.
> Standing rules: Both additions (comments + tests) · `vendor/bin/pint --dirty` ·
> full gate per stream (Unit → GroupLifecycle → Feature → render-pages →
> `route:list` 1482 → `BladeFinalizationTest` identical baseline).

## 1. Where things stand

**Done (all gated, changelogged in `Modules/Capstone/REFACTOR-CHANGELOG.md`):**
Batches A–I (Group 776→502, TaDefenseSchedule 805→458, Document 597→412,
SupervisorEvaluation 584→313, EXPO rejection + ADR 0012, engine phase weights
with strict `===` identity, 4+2 read-path Requests, backend glossary) plus
Stream 1–3 leftovers (8 GroupController writes extracted via new
`Exceptions/GroupActionException.php`, `UpdateWeightsRequest`,
`ScoreSummaryRequest`, Alpine `:`-on-component bug fixed module-wide —
render 75/0).

**Last verified gate:** Unit 43 · Feature 169 · render **75/0** · routes 1482 ·
pint clean · `BladeFinalizationTest` identical env failure (14 failed / 0 assertions).

**Locked decisions (do not relitigate):** behavior-preserving except blessed fixes
(PDC2 4-component, execute 500→400, eligibleStudents 500→200, update-path 500s→envelopes,
EXPO rejection, phase weights) · weights split indicator/component/phase ·
pooled average = submitted rows only · Requests shape-only, services own guards,
`HttpException` rethrown to bubble · fail-open side effects log · twin strategy =
verbatim-move, keep twins (reconciliation is Stream 1 below) · order: twins →
small sweep → env · sweep depth: Requests + pins only · whole-suite triage: yes.

**Known tripwire:** `tests/BladeCalendarAccessTest.php:650-663` calls controller
ready-methods directly — must stay green through Stream 1.

## 2. Stream 1 — Twin reconciliation (first)

`GroupService` holds endpoint methods alongside older twins with different
contracts. Preliminary survey (re-verify at build): only `handleJoinGroup`
(← `BursaIdeController:284`) and `adminForceDeleteGroup`
(← `BladeMonitoringController:120`) have live callers.

**Step 0 — blocking grill gate.** Build caller table
(`grep -rn "<name>" Modules/Capstone/{app,routes,tests} --include="*.php"`,
excluding `function` defs) + contract-diff table (guards/messages/codes/side
effects), present for sign-off. Delete nothing before approval.

| New (tested) | Twin | Recommended disposition |
|---|---|---|
| `createStudentGroupFromRequest` | `createGroup`, `createSoloGroup`, `ensureNoExistingMembership` | Delete (zero callers) |
| `deleteGroupCascade` | `deleteGroup(Group,User)` | Delete |
| `sendMemberInvitation` | `sendInvitation` | Delete if zero callers |
| `rejectInvitationAsStudent` | `rejectInvitation` | Delete; endpoint behavior wins (different recipient `inviter->user_id` vs `inviter_id`); record in changelog |
| `removeMemberCascade` / `leaveGroupCascade` | `removeMember`, `leaveGroup`, `handleLeaveGroup` | Delete if zero callers |
| `transitionReadyState` | `markReadyForFinalization`, `cancelReadyForFinalization` | Delete if zero callers |
| `assignSupervisor2ByLecturerId` | `assignSupervisor2` (User-id) | Delete only if no admin-UI caller (re-grep `BladeMonitoring`, `TitleManagement`; highest-risk row) |
| — | `handleJoinGroup`, `adminForceDeleteGroup`, `validateInvitationAcceptance`, `destroyIfEmpty`, `evaluateGroupReadiness`, shrink/limits/transition helpers, `adminDeleteGroup` | Keep |

**Step 1.** Delete one pair at a time → `php -l` → `GroupWritesTest` +
`BladeCalendarAccessTest` + Unit → `route:list` clean. Restore + report on any
surprise caller; do not redesign around it.

**Acceptance:** tables + sign-off in changelog · Unit 43+ / Feature 169+ green ·
service smaller · no new patterns.

## 3. Stream 2 — Small-controller sweep (Requests + pins only)

**Recipe (no deviations):** copy inline rules verbatim into a Request (admin →
`Requests/Admin/<Area>/`, else topical dir per `Assessment/`/`Grade/`
precedent) → swap signature → delete inline → `rules()` pin in a Unit
contract test (one new `<Area>RequestContractsTest` per chunk) → pint.
`$request->input()` reads stay. If a rule smells wrong: pin verbatim, log as
found-not-fixed (rule fixes are product decisions). Executor may add one
rejection test per smelly write endpoint (logged).

**Chunks (gate after each; `Controller:lines`):**
1. Config/template (9 sites): `AssessmentComponentTemplate:30,63` ·
   `PeerReviewIndicatorTemplate:29,51` · `DocumentType:18,39` ·
   `PeriodAssessmentConfig:29,88,155`.
2. Scheduling/rooms (8): `Location:74,118,164` · `Schedule:89,162` ·
   `Calendar:20` · `ScheduleRequest:77,150`.
3. Titles/bids (9): `Title:62,118` · `Bid:43,91,139` · `TitleApproval:139` ·
   `StudentProposal:46` · `Period:24,55` · `TitleManagement:24`.
4. Evals/registrations (10): `Evaluation:57` · `ExpoEvent:58,100` ·
   `TaRegistrationApproval:71` · `PeriodRegistrationApproval:81` ·
   `Stakeholder:28,44,67` · `DigitalSignature:17`.
5. Remainder (~50, split 5a/5b): `ReportSummary:21`, `ReportExport:22`,
   `File:16`, `Registration:43`, `AuthBridge:16,100`, `GradeConsistency:18,35,98`,
   `BladeUser:17`, `BladeMonitoring:22,32,68,84,118`, `Sempro:48,134,242,288,392`,
   `TaDefense:57,218,279,403`, `TaSubmission:44,91,153`, `ExpoStudent:95,137`,
   `ExpoController:115`, `PeerReview:96,174,215,231,251`, `BursaIde:99`,
   `IndividualTa:20`, `BladePeriod:73`, `SupervisorEvaluationAdmin:29`,
   `PeriodPeerReviewConfig:56,89`.

**Skipped (converted):** Finalization, Group, Document, SupervisorEvaluation,
AssessmentScore, GradeConfiguration, TaDefenseSchedule.

**Acceptance per chunk:** pins green · Feature green · routes 1482 · diff shows
only signatures + deleted inline blocks.

## 4. Stream 3 — Batch H env reds (investigate-first, last)

No app-code changes until the env cause is proven. (1) Run
`BladeFinalizationTest` verbosely; capture the exact failing migration
(suspect: another module's MySQL-only syntax under `phpunit.xml` sqlite
`:memory:`). Confirm `AdminTitle`/`BladeUsers` share the root cause — verify,
don't assume. Capstone offender → fix there; other module → proposal back
(no drive-by). (2) OOM: measure per-file peak first; ladder =
`php -d memory_limit=512M` (config-only) → shard run → isolated-sqlite
conversion (rework, last resort). (3) Update baseline numbers only when a red
turns green.

## 5. Stream 4 — Whole-suite triage (with Stream 3)

PHP: full `php artisan test --compact` once (Unit, then per-directory chunks).
JS: 11 `.mjs` suites in `Modules/Capstone/tests/` need node/vitest (check
`package.json` runner) — triage separately, do not fix here. Bucket every
failure (app-bug / env / known) into a changelog triage table. Fix only
Capstone app-bugs; anything outside the module returns as a separate proposal.

## 6. Build order

Stream 1 tables → sign-off → Stream 1 deletions → Stream 2 chunks 1–5 →
Stream 3 diagnose → Stream 4 triage → final gate + changelog.
