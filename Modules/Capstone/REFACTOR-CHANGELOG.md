# Capstone Module — Refactor Change Log

**Scope:** `Modules/Capstone/` only. **Net diff (module): +1,338 / −6,674 lines.** All changes are behavior-preserving extractions, unifications, and dead-code deletions, except 3 bug fixes explicitly marked below.

> Note: the working tree contains unrelated pre-existing dirty/untracked files (Blade component library, `resources/assets/*`, `public/build/*`, `package.json`, `scripts/build.mjs`, `docs/`, `CHANGES-UI-UNIFICATION.md`, `DESIGN-GUIDE.md`, `GLOSSARY.md`, `app/View/`, most `resources/views/pages/*` edits, `composer.lock`, `.codex/*`, `frontend/*`). Those are **not** part of this refactor and are excluded below.

**Verification baseline (must stay green):** `tests/Unit/` 14 → **21** (7 new) · `GroupLifecycleTest` 20 · Feature set (Expo×2, Examiner×2, Schedule×2) 52 · `render-pages.php` 74 rendered / 1 pre-existing `expo-detail` error · `route:list` clean · `pint` clean. Pre-existing failures (`BladeUsers`, `BladeFinalization`, `AdminTitle`, OOM set) reproduce identically on the unmodified baseline and were not touched.

## Phase 0 — Safety net

- Recorded baseline numbers above before changing anything; every later phase re-ran them.

## Earlier pass (already in tree when this changelog was written)

Provider unification (dead `Providers/CapstoneServiceProvider.php` deleted; `Group`/`GroupMember`/`Bid` observers registered; shell composer merged), route-view catalog unified (`BladePages` truth; 18 wrapper Blades deleted), `CapstoneAccess` + `CapstoneRole` merged, period gate unified (`PeriodGate`), group engine unified (jobs delegate to `GroupService::evaluateGroupReadiness`; missing `Group::determineStatus/getReadinessIssues/refreshReadinessSnapshot` implemented null-safe), `SemproScore`/`SidangTaScore` collapsed onto `HasAssessmentScore`, partial comment/test cleanup.

## Phase 1 — Trivial surplus + a real break found by verification

**Deleted:**
- `app/Providers/EventServiceProvider.php` (empty `$listen = []`, never referenced; routes load via `bootstrap/app.php`, confirmed by grep).
- `app/Providers/RouteServiceProvider.php` (unused indirection; same confirmation).

**Modified:**
- `app/Exceptions/ConflictRuleException.php` — now `extends DomainRuleException` instead of `Exception` (identical 10-line twins; catch sites keep working, `Conflict` still distinguishable).
- `app/Http/Middleware/CapstoneRoleMiddleware.php` — **bug fix:** its `handle()` signature (`string $expected = ''`) was incompatible with the parent (`?string $expected = null`) and fataled `route:list`. Now matches parent and forwards `'' → null`.

## Phase 2 — One grading engine

**Modified:** `app/Services/GradeCalculationService.php`, `app/Http/Controllers/{ReportDetailController, ReportExportController, GradeConfigurationController}.php`

- Added `GradeCalculationService::mean(?float ...$scores): ?float` (mean of non-null, round 2, else null) and `::peerReviewWeightedAverage($reviews): ?float` (weight-then-fallback-to-plain-average, byte-identical to the two controller copies).
- `GradeCheckController`: deleted `calculatePDC1/PDC2/TA/FinalScore` (4 copies of the mean shape) → direct `mean()` calls; inlined `calculateEvaluatorWeightedAverage()` (it just returned `$score->score`) at its 2 call sites.
- `ReportDetailController::calculatePeerReviewWeightedAverage` + `ReportExportController::peerReviewWeightedAverage` (byte-identical twins) → both delegate to the engine.
- `GradeConfigurationController::calculatePDC1Grade/calculatePDC2Grade` (near-duplicate ~55-line methods differing only in weights key/defaults/response keys) → one private `phaseGrade($groupId, $weightsKey, $defaults, $gradesKey, $finalKey)`.
- Deliberately **not** merged: engine's legacy `calculatePDC1/PDC2($groupId)` (unweighted fixed-type means, different contract) vs the weight-based grade endpoints.

## Phase 3 — `ReportDetailController` breakup (1915 → 72 lines)

**Created `app/Reports/`:** `AssessmentReport` (268), `PeerReviewReport` (348), `GradeReport` (304), `StudentEvaluationReport` (809), `GroupReport` (89), `CsvExporter` (202). All methods moved verbatim (scripted slice), then:
- 6 CSV privates → `CsvExporter` statics; `ReportExportController::streamCsv` + row streaming shared from it.
- Extracted `PeerReviewReport::summaryRows()` from `peerReviewSummary` (query + per-reviewee rows), reused by the JSON endpoint.
- Extracted `evaluateStudentEvaluations()` shared by summary + CSV export paths.
- `ReportDetailController` is now 12 one-line delegations; `ReportSummaryController` intentionally kept (cohesive dashboard aggregate, already delegates to the engine).
- **Bug fix:** `ReportExportController::exportPeerReviews` (summary mode) called `buildPeerReviewSummaryRows()`, which did not exist anywhere — the endpoint fataled. Implemented as `CsvExporter::buildPeerReviewSummaryRows()` on top of `summaryRows()`.

## Phase 4 — `FinalizationController` queries (1801 → 1365)

**Created `app/Finalization/FinalizationSupport.php`** (457 lines): moved all 13 read/guard helpers verbatim (`buildStats`, `buildFlow`, `groupPayload`, `resolveFinalizationActions`, `baseGroupQuery`, `paginateGroups`, `ungroupedStudentsQuery`, `paginateUngroupedStudents`, `checkReadyForExecute`, `applySupervisors`, `resolveStudents`, `hasMembershipInPeriod`, `assertTitleAssignable`) + the 4 status-set constants (`READY/FINAL/POST_FINALIZATION/NOT_READY_STATUSES`); action call sites updated (`$this->x` → `$this->support->x`, `self::` → `FinalizationSupport::`). Named *Support* (not *Queries*) because `applySupervisors` writes. Remaining actions are per-endpoint orchestration with writes and no endpoint coverage — left in place deliberately.

## Phase 5 — `SupervisorEvaluationController` split (1772 → 584 + 220 + 477 + presenter 535)

**Created `app/Evaluations/SupervisorEvaluationPresenter.php`** (11 read/format helpers moved verbatim). **Created** `SupervisorScheduleController` (groups/pendingCount/schedules) and `SupervisorEvaluationAdminController` (adminScheduleSummary/exportScheduleSummary/getGradesForSchedule); original keeps form/expoReview/reviewExpo/store. **Deleted** dead `formatStatusBasedEvaluationForSupervisor` (defined, zero callers). `routes/api.php` bindings updated (10 references); `route:list` verified. **Test fix:** `ExaminerDeadlineTest` invoked private `resolveSoftDeadline` on the controller via reflection — retargeted to the public presenter method (internals→behavior, per test rules).

## Phase 6 — One title-acquisition vocabulary

**Created `app/Services/TitleAcquisitionService.php`**: `membership/leaderMembership/requireLeader/lockedGroup/requireBidAction/requireProposalAction` (collapses the 4-line membership→leader→lock→flow preamble; HTTP codes/messages preserved byte-identically, including the destroy/reorder `'Bidding is locked.'` literal).
**Modified `app/Support/StudentTitleAccess.php`:** 5 status lists centralized as named constants (`PROPOSABLE/BIDDABLE/BIDDABLE_SOLO/RECRUITABLE/SOLO_STATUSES`, lifecycle docs preserved); `proposalFlow/bidFlow/periodReason` logic unchanged.
**Modified controllers:** `BidController` (DI + 3 preambles), `StudentProposalController` (DI + 2 preambles + inline status list → const), `SoloTitleController` (local `RECRUITABLE_STATUSES` deleted), `BursaIdeController` (local `SOLO_STATUSES` deleted; cross-controller `SoloTitleController::RECRUITABLE_STATUSES` reference killed). `SOLO_STATUSES` ≠ `RECRUITABLE_STATUSES` kept as distinct lists (different semantics). Writes intentionally stay in controllers (no endpoint coverage).

## Phase 7 — One document listing home

**Created `app/Documents/DocumentQueries.php`** (259 lines): `getPhaseDocuments/getExpoDocuments/getTaDocuments/formatTaDocument/getGroupMembers` moved verbatim from `Admin/DocumentUploadController` (470→221 lines, same `Request` signatures — zero behavior risk); controller injects it. `FileController` already thin, untouched. Recorded (not changed): `DocumentController::PHASES/UNLOCK_RULES` vs `WorkflowService` divergence (TA/SIDANG vs TA_DRAFT — needs a product decision).

## Phase 8 — One config-guard pattern

**Modified `app/Support/PeriodGate.php`** (+`lockedPeriod/assertNoGroups/assertNoGroupsIfChanged`) and both config controllers (6 copies of lock + finalized + groups-exist preamble → guard calls). Typed/untyped + legacy-schema branches deliberately not forced into one generic (different contracts). Legacy fallback paths byte-identical.

## Phase 9 — `GroupService` presentation extraction (1683 → 1481)

**Created `app/Groups/GroupPresenter.php`** (333 lines): 10 display/payload methods moved; `GroupService` keeps one-line forwarders (zero caller churn: `GroupController`, `BladeMonitoringController`, `FinalizationSupport` untouched). Also unified: `resolveStatusLabel` → special READY_FOR_BIDDING+title branch + admin map (verified equivalent); `resolveAllowedActions` lock-list → `RECRUITABLE_STATUSES` (same set). `validateInvitationAcceptance` stays (domain validation, not display).

## Phase 10 — Comments (chosen) + Tests (chosen), done last

- Removed ~70 restater comments in moved code (`// Apply filters`, `// Manual pagination`, …); kept contract/why notes (frontend `component_display`/`camelCase` contract, legacy-vs-period-component fallback, preload/clear protocol, SV1 chain, honesty note, examiner-column note — restored after one over-deletion).
- `AlertComponentTest` rewritten to behavior (old asserted exact Tailwind scales + `<svg`; icons render `<i class="ph…">`); removed const-copy assertion in `EditableStatusesTest`; added `GradingCombinatorsTest` (5 tests: `mean`, weighted/fallback/null peer-review average) and `GroupPresenterTest` (2 tests: title-dependent + map labels) — Unit 14→21.

## Phase 11 — Extra surplus caught by the checklist

- **Deleted dead `GradeCheckController` (1123 lines) + orphan `GradeCheckQueryRequest`**: zero routes, zero callers, and its `summary()` queried nonexistent `groups`/`final_grades` tables.
- `TaDefenseScheduleController` 4 notify methods → 2 audience helpers (messages verbatim).
- Final: Unit 21 · GroupLifecycle 20 · Feature 52+41 · render 74/1 · `route:list` clean · `pint` clean.

## Bugs fixed along the way (structural finds)

1. `CapstoneRoleMiddleware` signature fatal on `route:list`.
2. Missing `buildPeerReviewSummaryRows` — peer-review summary export fataled.
3. Observer audit writes threw out of `$group->save()` when `capstone_audit_logs` was absent (minimal test schemas) — audit is now fail-open; this also hardens production.
4. Jobs/observers called `Group::refreshReadinessSnapshot()/determineStatus()/getReadinessIssues()`, which did not exist — implemented on the model (null-safe for minimal schemas), jobs delegate to `GroupService::evaluateGroupReadiness` with fail-open handling.

## Known remaining items (deliberate, with rationale)

- Controllers still >450 lines (`Finalization` actions, `TaDefenseSchedule`, `Group`, `Document`, `SupervisorEvaluation` submission): endpoint-orchestration with writes and no endpoint coverage — reads extracted, writes stay until coverage exists.
- Ghost statuses (`TITLE_PROPOSED`, `PENDING`/`REJECTED` in proposable list, progress-list extras) and workflow TA/TA_DRAFT divergence: need product decisions, not silent edits.
- Pre-existing failures unchanged: `BladeUsers`, `BladeFinalization`, `AdminTitle` (migration env), Blade-set OOM at 128M, 1 pre-existing `expo-detail` render error.

## Phase 12 — Untangle pass 2 (this session, no commits)

Additions chosen: **Both** (restater comments removed in touched code; weak tests removed, behavior tests added — Unit 21 → **33**).

**P0 — refactor-introduced break, fixed:**
- `FinalizationSupport` called `$this->support->...` in 5 places (`buildStats`, `groupPayload`, `paginateGroups`, `paginateUngroupedStudents`) but has no `$support` property — every finalization read fataled. Now `$this->...`. `groupPayload` also called `GroupService::transformGroupForAdminList`, which never existed on the service — now injected `GroupPresenter`.
- Deleted dead `GroupPresenter::ensureNoExistingMembership` (private, zero callers, threw unimported `DomainRuleException` — fatal if ever called). Live copy stays in `GroupService`.

**B — one workflow-phase vocabulary.** Created `Support/WorkflowPhases.php` (engine `PHASES`/`UNLOCK_RULES`/`STATUS_GATES`, `UI_PHASES` stepper list, `TA→TA_DRAFT` alias, `nextPhase()`). `WorkflowService` consts are now aliases + `getNextPhase` delegates; `DocumentController::PHASES` and `PhaseDocumentRequirementController::PHASES` alias `UI_PHASES` (values unchanged, `StepperPhaseContractTest` green). UI list and engine list stay deliberately distinct (stepper needs `TA`/`SIDANG` display codes); the alias mapping is now explicit instead of hidden. Side effect: admin requirement `summary()` order is now TA-before-EXPO, matching the stepper.

**C — one group-status vocabulary.** `GroupStateMachine::ALL_STATUSES` now includes `TA_DRAFT`, `READY_FOR_TA_INDIVIDUAL`, `TA_IN_PROGRESS`, `DISSOLVED` (order-preserving: earlier `isAtLeast` comparisons unchanged; no transition edges added). Added `isSolo()` (flag OR `FORMING_SOLO` status) adopted in `StudentTitleAccess::bidFlow`, and `isLifecycleSticky()` (complement of the 4 formation states — identical to the old inline list for all 20 known states) now backing `Group::determineStatus`. Removed write-nowhere ghosts: `TITLE_PROPOSED` (only ever appeared in the two read lists) from `FinalizationSupport::NOT_READY_STATUSES` and the presenter progress order (now `GroupStateMachine::ALL_STATUSES`); `PENDING`/`REJECTED` (bid/approval states, never `groups.status`) from `PROPOSABLE_STATUSES`. Progress `%` for early statuses shifts to canonical order positions.

**D — one document-workflow reader.** `WorkflowService::phaseShape()` (required-types + `GENERAL` fallback + latest-per-type + approval flags) now serves `getWorkflowData`, `getNextPhaseRequirements` (pending/approved derived from the shape), and `DocumentController::workflow`. Latest-doc lookup lives in exactly one place.

**E — one grade-math kernel.** `scoreWeight()` (template → component → 1.0) backs all 3 weighted-average sites; `combinePeerRows()` (indicator-weighted `raw_score*25`, plain-average fallback) backs all 3 peer-review paths. Rounding preserved per caller (kernel unrounded; static keeps its `round`). Two silent divergences fixed to the shipped/reported semantics: single-query peer average used `score×(weight ?? 1)` (wrong scale, swallowed missing relations as null-grade via `try/catch`) and cache peer average had no fallback — both now match `peerReviewWeightedAverage`. Legacy group-level `calculatePDC1/PDC2` + `recalculateAndNotify` stay (live callers in `SupervisorEvaluationController`); `EXPO` stays in the score repository (live write path in `AssessmentScoreController`).

**F — wrapper collapse + DI.** Deleted 10 `GroupService→GroupPresenter` forwarders; `GroupController` injects the presenter (10 sites retargeted). Deleted `TitleAcquisitionService::membership` passthrough (callers already used `StudentTitleAccess`); deleted single-use `ResolvesActivePeriods` trait (`PeriodGate::activeAndFinalizedIds()` inline). `GroupPresenter` takes `WorkflowService` via ctor (no more `app()` + swallowed error inside); `FinalizationSupport` takes `GroupPresenter` + `SupervisorLoadService` (write service kept only for `applySupervisors`).

**G — special cases, comments, tests.** `Group::refreshReadinessSnapshot` now `report($e)` on fail-open; grade-calc `try/catch→null` kept deliberately (batch resilience, already `Log::error`, contract is `?array`). Restater comments removed in touched `DocumentController` regions. New: `GroupStatusesTest` (5: sticky partitions, ordering, `isSolo`, phase alias/next), `FinalizationSupportTest` (5: action matrix incl. ghost rejection), `GradingCombinatorsTest` +2 (mixed-weight rows, null-`raw_score` skip).

**Verification:** Unit **33** green · GroupLifecycle 20 green · `tests/Feature` 105 green · render 74/1 (same pre-existing `expo-detail` error) · `route:list` 1482 clean · `pint` clean · `BladeFinalization` still fails identically at migration setup (0 assertions, env, untouched by this pass).

**Still open (needs product decisions, not silent edits):** `POST_FINALIZATION_STATUSES` partition for `READY_FOR_TA_INDIVIDUAL`/`TA_IN_PROGRESS`/`DISSOLVED`; `DocumentController::UNLOCK_RULES` UI map vs engine `UNLOCK_RULES` overlap; `GroupService:385` now-redundant `DISSOLVED` special-case next to `isAtLeast`.

## Phase 13 — Grilled decisions + finalization extraction (this session, no commits)

Grill resolved all four deferred decisions: (1) all three end-states → post list; (2) PDC2 unifies on 4 components (3-component path was a bug); (3) divergence test, maps stay separate; (4) Finalization first. Reviewed against `.opencode/skills` (laravel-architecture, untangle-codebase; alpinejs-expert N/A — backend-only): writes target the existing `FinalizationService` (never a new class), Form Requests per repo convention, batch-only grade aggregation (no N+1).

**Step 1 — partition.** `POST_FINALIZATION_STATUSES` += `READY_FOR_TA_INDIVIDUAL`, `TA_IN_PROGRESS`, `DISSOLVED`. `FinalizationSupportTest` +1 (all three resolve `POST_FINALIZATION` reason). Every machine status now belongs to exactly one partition.

**Step 2 — unlock agreement.** New `WorkflowUnlockAgreementTest` (3 tests): shared phases agree after `TA→TA_DRAFT` normalization; `SIDANG→EXPO` allow-listed as stepper-only; `STATUS_GATES` ⊆ engine phases. Maps stay separate by design.

**Step 3 — group PDC2 on 4 components.** `calculatePDC2($groupId)` gains pooled `PEER_REVIEW` via new `getGroupPeerReviewAverage()` (one query, same `combinePeerRows` kernel as student-level — matches the pooled semantics of the sibling group components); shared `peerRows()` adapter kills the third row-building copy. Student/single paths untouched. `recalculateAndNotify` inherits (callers discard the return). Fixture extended (`period_indicator_id`/`raw_score`/`is_final_submission` on peer reviews + indicator tables); `ExpoSelfEvalGradesTest` +2: peer case asserts 4-component shape (NILAI 80 + EXPO 84 + PEER 100 → 88.0), no-peer case pins legacy-identical values. **Grade-shift warning:** groups with peer-review data now grade differently (that was the bug).

**Step 4 — finalization writes extracted.** New `FinalizationWritesTest` (15 tests, isolated SQLite, controller-driven via framework-faithful FormRequest + `validateResolved()`): execute (+validation rejection, +ineligible normalization below), rollback, cancel (payload shape), reopen (PDC1 revert + deeper-states report), forceReady, assignTitle (FORMING→TITLE_APPROVED), promote (title-required rejection), setSupervisor+mark_final, batch (1 success/1 failure), finalize-flag+PDC1, manual-group (solo + leader), add-members over-max rejection. 15 Form Requests in `Requests/Admin/Finalization/*` replace all 15 inline validations on write paths (reads keep theirs). 12 write bodies moved to `FinalizationService` (`executeFinalization`, `flagFinalizePeriod`, `rollbackFinalization`, `cancelKelompokFinal`, `assignGroupTitle`, `promoteGroupToReady`, `createManualGroup`, `addStudentsToGroup`, `reopenPeriod`, `forceGroupReady`, `setGroupSupervisors`, `batchSetGroupSupervisors`); controller methods are thin wrappers (input → service → `ok()`/400). New `UnprocessableFinalizationException` carries the 422 ineligible list (lone non-400 branch, preserved exactly). **Intentional fix:** execute's no-eligible case was an unhandled 500; normalized to the sibling 400 envelope with message preserved (test pins it). Controller 1365 → ~820 lines; dead `GroupService`/`GroupStateMachine` deps removed from its ctor. `batchSetSupervisor`/`lock`/`unlock`/`autoFix` were already thin delegations — untouched.

**Step 5 — close-out.** Removed redundant `GroupService:383` `DISSOLVED` disjunct (covered by `isAtLeast` range).

**Verification:** Unit 37 green · GroupLifecycle 20 green · full `tests/Feature` green (126 total incl. 15 new) · render 74/1 (same pre-existing error) · `route:list` 1482 clean · `pint` clean · `BladeFinalization` identical env failure (14 failed, 0 assertions).

**Remaining:** `>450`-line controllers elsewhere (`TaDefenseSchedule`, `Group`, `Document`, `SupervisorEvaluation` submission) still await coverage-first extraction; `EXPO` repository contract stays (live write path); pre-existing reds unchanged.

## Batch A — GroupController thinning + GroupService extraction (this session, no commits)

**Created:** `Requests/Group/CreateStudentGroupRequest.php` (`period_id` required|integer|exists) + `Requests/Group/ChangeReadyRequest.php` (`group_id` required|integer|exists), following the `AddMemberRequest`/`AssignSupervisor2Request` convention. All 4 inline `$request->validate` calls in `GroupController` replaced (`createStudentGroup`, `changeReady`, `addMember`, `assignSupervisor2`); controllers now pass only `$request->validated()`.

**Extracted to `GroupService`:** `transitionNewGroupToReadyIfEligible()` (verbatim move of the `checkAndTransitionToReady` FORMING→READY_FOR_BIDDING fast path; why-comment records why it stays separate from `evaluateGroupReadiness` — min fallback 2 vs 3, no quota/title checks) adopted at both call sites (create + accept-invite); `assignSupervisor2ByLecturerId()` (Lecturer-id lookup + dosen-role + same-as-sup1 guards throwing `DomainRuleException` with identical messages, transaction body unchanged) backing a thin `assignSupervisor2` wrapper that maps guards to the same 400 envelopes. Removed now-unused `Lecturer`/`Supervision` imports and the dead controller private.

**Tests:** new `tests/Feature/GroupWritesTest.php` (18 tests, isolated SQLite): 4 Request contracts (rules shape + accept/reject), size limits (config + fallbacks), undersized guard message, `destroyIfEmpty` keep/delete + audit, shrink adequate-shape, ready transition + non-FORMING no-op, supervisor-2 reject-unknown / reject-same / persist (+supervision row).

**Verification:** Unit 37 green · GroupLifecycle 20 green · full `tests/Feature` 107 green (89 existing + 18 new) · render 74/1 (same pre-existing `expo-detail` error) · `route:list` 1482 clean · `pint` clean · `BladeFinalization` identical env failure (14 failed, 0 assertions).

**Remaining:** invitation accept/reject, member remove/leave, and delete-group bodies still live in the controller (larger moves needing their own characterization); `TaDefenseSchedule` (Batch B) next.

## Batch B — TaDefenseScheduleController extraction (this session, no commits)

**Tests first:** new `tests/Feature/TaDefenseScheduleWritesTest.php` (12 tests, isolated SQLite). EOffice boundary resolved at the service-locator seam: `FakeAvailableEoffice`/`FakeConflictEoffice` bound for `EofficeAvailabilityService::checkByRoom`, one `eo_mr_ruangans` row seeded for the `Ruangan::findOrFail` name lookup. No changes to `Modules/EOffice`. Covers: non-admin rejection, not-in-group, unapproved submission/registration, duplicate defense, supervisor-as-examiner, conflict path (zero rows written), happy-path side effects (schedule + pivot + `TA_READY_FOR_SIDANG` submissions + 2 PENDING evaluations/examiners + notifications), cancel revert + audit, DONE-cancel rejection, eligible scoping (ready flag + active-defense exclusion).

**Intentional fix (same class as Phase 13 execute 500→400):** `eligibleStudents` active-defense query used `with('students:id')`, which 500s because `App\Student::$with = ['user']` needs the dropped `user_id`. Now full `students` select; the exclusion test pins it.

**Extracted to `SchedulingService`:** `scheduleTaDefense()` (8 guards → `DomainRuleException` same messages, conflicts → new `ScheduleConflictException` carrying detail, nested transaction + notifications verbatim), `updateTaDefenseSchedule()` (10 guards; evaluation-lock maps to `AuthorizationException` → same 403; previously-unhandled 500s now return the same `Failed ...` envelope shape as siblings), `cancelTaDefenseSchedule()` (revert + audit + notify), plus the 4 live notify helpers. Deleted dead `notifyStudentsAddedToDefense` (zero callers). Controller keeps admin authorization + envelopes + all reads (`index/show/mySchedule/examinerSchedules/eligibleStudents`); `update`/`assignExaminers` share the service path as before. No new Form Requests (the three already existed and are reused). Controller 805 → 458 lines; 8 unused imports removed.

**Verification:** Unit 37 green · GroupLifecycle 20 green · `GroupWritesTest` 18 green · full `tests/Feature` 119 green (107 + 12 new) · Schedule/Examiner suites 37 green · `route:list` 1482 clean · `pint` clean.

## Batch C (part 1) — DocumentController extraction (this session, no commits)

**Tests first:** new `tests/Feature/DocumentWritesTest.php` (9 tests, isolated SQLite, faked `DocumentStorageService` subclass recording store/delete). Security pin: forged PDC2 POST from a PDC1 group → 403 with the phase-gate message, zero rows, storage untouched — UI gating alone never authorizes. Characterization also pinned a real precedence: the workflow gate runs before the store's approved-replace guard (an APPROVED doc with no requirements reports `completed`, not the replace message). Covers: first upload 201, revise 200 + old-file delete, configured `document_type` 422, dosen approve + member notify, non-supervisor 403 (bubbled `HttpException`, unchanged), bad status 422, expo-branch REJECTED path.

**Requests:** `Requests/Document/StoreDocumentRequest.php` (phase ∈ `UI_PHASES`, file mimes/max, dynamic `document_type` from the caller's period requirements — fail-open to nullable when unresolvable, the service returns the 400 then) + `ReviewDocumentRequest.php` (shared APPROVED/REVISE/REJECTED + feedback rules for both review branches).

**Extracted to `DocumentStorageService`:** `submitDocument()` (membership 400, workflow-gate + approved-replace 403s via `AuthorizationException` with identical messages, storage + upsert verbatim; the workflow shape is computed by the controller's reader and passed in — never recomputed, so no duplicated gate logic), `reviewDocument()` (expo + phase branches verbatim incl. `abort_unless` 403s, `IndividualTaWorkflow` sync, notifications, lifecycle advance), private `checkPhaseCompletion()`. Constructor gains `NotificationService`, `GroupLifecycleService`, `IndividualTaWorkflow`, `GroupStateMachine` (deps widened `private readonly` → `protected` so the test fake initializes the same properties — no behavior change). Deleted dead `notifyStudentsAddedToDefense` analogue: none here. Removed dead `GroupStateMachine` dep + `DB`/`Rule`/`GroupLifecycleService`/`NotificationService` imports from the controller; `update()` rethrows `HttpException` so supervisor 403s bubble exactly as before, other failures return the sibling 500 envelope (previously unhandled). Controller 597 → 412 lines.

**Verification:** Unit 37 green · full `tests/Feature` 128 green (119 + 9 new) · `route:list` 1482 clean · `pint` clean.

**Remaining (Batch C part 2):** `SupervisorEvaluationController::reviewExpo` + `store` → supervisor-evaluation service (needs ExpoRegistration/ExpoSelfEvaluation/assessment-score/formAccess characterization); then Batches D–G.

## Batch C (part 2) — SupervisorEvaluationController extraction (this session, no commits)

**Tests first:** new `tests/Feature/SupervisorEvaluationWritesTest.php` (12 tests, isolated SQLite, legacy component schema — no period table, so the legacy `component_id` path is pinned; the period path is owned by existing suites). Covers reviewExpo codes exactly (403 supervision/window, 404 missing registration, 409 unsubmitted, 422 missing self-eval, 200 approve + 2 member notifies, 200 reject + feedback) and store (403 supervision/window, 400 non-member with zero rows written, 422 missing component, 200 upsert + idempotent resubmit with in-place score update).

**Requests:** `Requests/SupervisorEvaluation/ReviewExpoRequest.php` (APPROVED/REJECTED + required-if-rejected feedback) + `StoreSupervisorEvaluationRequest.php` (schema-aware dual component fields via the same `hasTable` check, so either field survives validation).

**New `Services/SupervisorEvaluationService.php`:** `reviewExpoRegistration()` (supervision + `formAccess` window guards, error-array transaction 404/409/422, member notify + PDC2 recalc + lifecycle advance fail-open — all verbatim) + `submitEvaluationScores()` (schema resolution, per-item component guard 422, supervision 403, `formAccess` 403, membership 400, deadline resolve, batch upsert with 500 envelope on unexpected failure, completeness recalc + lifecycle + late-notify fail-open). Results reuse the controller's existing error-array idiom, so every envelope is byte-identical; `findOrFail` lookups keep bubbling to the handler as before. Controller keeps lecturer resolution + envelopes; constructor-injected service. Controller 584 → 313 lines; `DB`/`Schema`/`Log`/`GradeCalculationService`/`NotificationService` imports removed.

Schema notes pinned by the tests: soft-deadline persistence needs `capstone_seminar_schedules`; the repository upsert needs the production unique key on `(component_id, evaluator_id, group_id, student_id)`; the window reason lists statuses (not the type name).

**Verification:** Unit 37 green · full `tests/Feature` 140 green (128 + 12 new) · `route:list` 1482 clean · `pint` clean.

**Remaining:** Batches D–G.

## Batch D — EXPO write rejection + ADR 0012 (this session, no commits)

Locked decision executed: `AssessmentScoreController::index` + `store` no longer accept `EXPO` (new `Requests/Assessment/ScoreFormRequest.php` + `StoreScoresRequest.php` with the same rules minus `EXPO`; the `in` message names the Expo self-evaluation flow as the correct path). `summary` keeps serving old EXPO rows read-only for audit — untouched.

**Tests:** new `tests/Feature/ExpoWriteRejectionTest.php` (4 tests, isolated SQLite): store EXPO → 422 with the self-evaluation redirect and zero rows written; index EXPO → 422; BIMBINGAN control still 201s with a persisted row; summary still returns the legacy weighted average (84.0) for an old EXPO row. `ExpoSelfEvalGradesTest` 7 green (self-eval grading unchanged).

**Docs:** `docs/adr/0012-expo-write-rejection.md` (reject-vs-dual-write-vs-migrate trade-off, grade truth at stake).

**Verification:** Unit 37 green · full `tests/Feature` 144 green (140 + 4 new) · `route:list` 1482 clean · `pint` clean.

## Batch E — Engine adopts phase weights (this session, no commits)

Locked decision executed: `GradeCalculationService` owns the single weighted math; `GradeConfigurationController::phaseGrade` + `calculateTAGrade` delegate with byte-identical envelopes (same keys, same rounding, same missing-type reporting).

**Engine:** `phaseWeights()` (config `grade_configuration[phase]` or defaults) + `weightedGroupPhaseGrade()` (repository averages with the `isSupportedType` guard, so `PEER_REVIEW` stays reported-not-evaluated as before) + `weightedStudentPhaseGrade()` (per-student TA path, no guard — unsupported types bubble exactly as the endpoint did). Docblocks fix the canonical terms (phase vs component vs indicator weight). Controller drops its two math copies and the now-unused repository import.

**Tests:** new `tests/Feature/PhaseWeightsTest.php` (6 tests, isolated SQLite): unconfigured PDC1 strictly identical (`===`: 85.0/42.5/70.0/35.0/77.5/100), endpoint envelope unchanged, configured PDC2 weights combine (70/30 → 74.0; unsupported `PEER_REVIEW` reported), defaults without config (35.0/50 — proving configured differs), TA delegation (85.0), missing types reported-never-zero-filled (final null). Whole-number strictness is pinned engine-direct; endpoint asserts use `assertEquals` because JSON drops `.0` (representation detail, not math). Schema notes: all seven split score tables must exist (supported types query even when empty); the upsert unique key from Batch C-p2 applies.

**Verification:** Unit 37 green (incl. grade combinators) · `ExpoSelfEvalGradesTest` green via full suite · full `tests/Feature` 150 green (144 + 6 new) · `route:list` 1482 clean · `pint` clean.

## Batch F — Read-path Form Requests (this session, no commits)

Uniformity only, no logic moves: `FinalizationController::dashboard/simulate/export/autoFix` now type-hint `Requests/Admin/Finalization/DashboardRequest.php`, `SimulateRequest.php`, `ExportRequest.php`, `AutoFixRequest.php` (rules copied verbatim from the inline validations). Reads keep using `$request->input()` — validated data merges identically.

**Tests:** new `tests/Unit/FinalizationReadRequestsTest.php` (4 rule-shape pins, no DB).

**Verification:** Unit 41 green (37 + 4 new) · full `tests/Feature` 150 green · `route:list` 1482 clean · `pint` clean.

## Batch G + done-criteria sweep (this session, no commits)

**Glossary:** `GLOSSARY.md` gains a `## Backend` section (UI section untouched): partition, pooled average (submitted-rows-only), write-orchestration vs reads, TA vs TA_DRAFT, SIDANG, dissolved, indicator/component/phase weights — each 1–2 sentences with `_Avoid_`, implementation-free per `GLOSSARY-FORMAT.md`.

**Sweep (manual, no subagents):** `TITLE_PROPOSED` appears only in a comment noting its intentional absence — no code ghost. Duplicated status/phase lists: single homes (`WorkflowPhases`, `GroupStateMachine::ALL_STATUSES`, agreement-tested controller maps). `app()` locators: new code adds zero — remaining hits are pre-existing outside touched regions (`GroupAutoFixService`, `AutoMatchmakerService`, `GradeCalculationService` flag service, `SchedulingService` EOffice seam + two older call sites; removing them is separate behavior risk). New catch blocks all rethrow, map to envelopes, or `Log`; fail-open blocks log. New comments are why-comments (gate precedence, fake boundaries, envelope mapping, term canon).

**Untangle done-gates:** typical change lands in one place (writes per service, reads via support/presenter); imports point at equal-or-lower volatility (controllers → services → models/support); behavior identical except blessed fixes (PDC2 4-component scale, execute 500→400, `eligibleStudents` constrained-eager 500→200, update-path 500s → envelopes, EXPO write rejection, engine phase weights).

**Pre-existing reds (Batch H, untouched, still identical):** `BladeUsers`, `BladeFinalization` (14 failed / 0 assertions, migration env), `AdminTitle`, Blade-set OOM at 128M, 1 `expo-detail` render error (render 74/1).

## Remaining work (this session, no commits)

### Stream 2 — Grade request uniformity

New `Requests/Grade/UpdateWeightsRequest.php` (12 weight rules, verbatim) backing `GradeConfigurationController::updateWeights`, and `Requests/Assessment/ScoreSummaryRequest.php` (keeps `EXPO` — summary serves old rows read-only per ADR 0012) backing `AssessmentScoreController::summary`. The summary test call site now drives through the Request. New `tests/Unit/GradeRequestContractsTest.php` (2 rule-shape pins; `Rule::in` objects asserted via string form).

### Stream 1 — GroupController write leftovers extracted

`GroupWritesTest` grows to 37 tests (isolated SQLite; new tables: invitations, notifications, period registrations, supervisor proposals). Characterization pinned two unreachable defense-in-depth branches (delete-finalized 400 and add-full 400 both lose to the presenter lock — 403 `HttpException` rules) and soft-delete member rows. New `Exceptions/GroupActionException.php` (message + HTTP code) preserves every JSON envelope exactly: `abort_*`/actor `HttpException` rethrown to bubble, `GroupActionException` mapped, unexpected failures keep the original `Failed …` 500 prefixes (create/changeReady/reject had none — still bubble).

New `GroupService` methods (verbatim moves; presenter action-matrix gates stay in the controller): `createStudentGroupFromRequest`, `deleteGroupCascade`, `acceptInvitationAsStudent`, `rejectInvitationAsStudent`, `removeMemberCascade` (+ `triggeredBy` audit parity), `leaveGroupCascade`, `transitionReadyState`, `sendMemberInvitation`. Twin-diff table: `rejectInvitation` twin notifies `inviter_id` (profile) with boolean flag vs endpoint `inviter->user_id` (app) with raw expression — kept separate, documented on the methods. Controller 776 → 502 lines; `GroupStateMachine` dep + 8 dead imports removed.

### Stream 3 — Alpine/Blade binding bug class fixed, render 75/0

`expo-detail/index.blade.php` put Alpine expressions into Blade-component bound attributes (`:class="data.…"` on `x-capstone::icon`), which Blade evaluates as PHP → the render gate's `Undefined constant "data"` error. Fix: `x-bind:` equivalents (pass-through attributes, Alpine resolves at runtime) for `:class` (1), `::disabled` (3), `::id`/`::readonly` (2) in that page. Sibling sweep: the identical dead `::disabled=` pattern (literal passthrough — buttons never actually disabled) fixed module-wide — 150 occurrences across ~30 pages/components (`data-table`, `pagination`, `counter`, `bimbingan`, `evaluation-form`, `expo-review`, admin pages) → `x-bind:disabled=`. Legit `:prop="$phpVar"` bindings (options, names, placeholders) intentionally untouched.

**Verification:** Unit 43 green · full `tests/Feature` 169 green (150 + 19 new) · render **75/0** (was 74/1) · `route:list` 1482 clean · `pint` clean · `BladeFinalization` identical env failure.

## Stream 1 — Twin reconciliation executed (this session, no commits)

**Caller table (verified repo-wide grep before deletion):** zero live callers for `createGroup`, `createSoloGroup`, service `deleteGroup(Group,User)`, `sendInvitation`, `rejectInvitation`, service `removeMember`/`leaveGroup`, `handleLeaveGroup`, service `markReadyForFinalization`/`cancelReadyForFinalization`, User-id `assignSupervisor2`, `adminDeleteGroup`, and (consequential) `resolvePeriod`. Live keep-callers: `createStudentGroupFromRequest` ← `GroupController:180`; `deleteGroupCascade` ← `:232`; `sendMemberInvitation` ← `:267`; `accept/rejectInvitationAsStudent` ← `:288/:306`; `remove/leaveGroupCascade` ← `:349/:386`; `transitionReadyState` ← `:203`; `assignSupervisor2ByLecturerId` ← `:486`; `handleJoinGroup` ← `BursaIdeController:284`; `adminForceDeleteGroup` ← `BladeMonitoringController:120`. Remaining `markReady*`/`deleteGroup` hits are controller methods and route/test call sites, not service twins. **Sign-off:** approved deletions 2026-11-11.

**Deletions (`GroupService` 1813 → 1193 lines, 20 public methods left):** `createGroup` + `createSoloGroup` + private `ensureNoExistingMembership` (only used by those two) · service `deleteGroup` · `sendInvitation` + `rejectInvitation` (endpoint recipient `inviter->user_id` wins, recorded) · `handleLeaveGroup` + service `removeMember` + service `leaveGroup` · service `markReady/cancelReadyForFinalization` (controller methods of the same name stay — they drive `transitionReadyState`) · User-id `assignSupervisor2` (docblock on `assignSupervisor2ByLecturerId` updated: sole method because id spaces differ) · `adminDeleteGroup` (docblock on `adminForceDeleteGroup` updated: sole admin path) · consequential `resolvePeriod` (only the deleted create-twins used it; `FinalizationController::resolvePeriod` is its own private method, untouched). No other file referenced the deleted methods (verified post-delete grep).

**Stale-tripwire repair (Both additions — tests):** `BladeCalendarAccessTest` group tests were red on baseline for two pre-existing staleness bugs unrelated to the twins: plain `Request` passed to controller methods now typed `ChangeReadyRequest`/`CreateStudentGroupRequest` (Batch A), and line 656 calling `GroupService::resolveAllowedActions()` which lives on `GroupPresenter`. Added `changeReadyRequestFor()`/`createGroupRequestFor()` helpers (mirroring `GroupWritesTest::callChangeReady`) and pointed the actions assertion at `GroupPresenter`. The 2 tripwire tests now pass (17 assertions).

**Verification:** Unit 43 green · full `tests/Feature` 169 green · `BladeCalendarAccessTest` 49 passed / 27 failed — baseline without my changes is 47/29, so zero new failures (remaining 27 are pre-existing `GradeReport`/`ReportDetailController`/`SupervisorEvaluation` DI/Blade-HTML issues in untouched files) · render 75/0 · `route:list` 1482 clean · `pint` clean.

## Stream 2 Chunk 1 — Config/template Requests (this session, no commits)

Nine inline validations → Form Requests (verbatim copies, shape only): `Requests/Assessment/StoreComponentTemplateRequest` + `UpdateComponentTemplateRequest` (store/update), `StoreIndicatorTemplateRequest` + `UpdateIndicatorTemplateRequest`, `Requests/Document/StoreDocumentTypeRequest` + `UpdateDocumentTypeRequest`, `Requests/Assessment/ShowAssessmentConfigRequest` (discarded-validation shape: statement replaced by a bare `$request->validated()` call, downstream `$request->type` reads untouched) + `StoreAssessmentConfigRequest` + `CopyAssessmentConfigRequest`. Controllers show only signature swaps + deleted inline blocks; `Illuminate\Http\Request` imports removed where unused.

Two mechanical adaptations (logged, not behavior changes): update-sites embed the route id in `unique:…'.$id`, which rules() cannot see — resolves via `$this->route('assessment_template')` / `$this->route('peer_review_indicator_template')` (apiResource param names, verified via route:list); `copy()` embeds the controller's `$periodId` in `not_in:` — resolves via `$this->route('period')` (same value, route supplies that argument positionally). Placement follows the mirror rule: PeriodAssessmentConfigController is not under Admin/, so its Requests live in `Requests/Assessment/`.

Surfaced, not fixed: `PeerReviewIndicatorTemplateController` has zero routes (verified via route:list + routes/*.php grep) — dead surface. Converted anyway per sweep scope; route registration (or deletion) is a product decision, returned as proposal fodder.

FOUND-NOT-FIXED: none. Smelly (wrong-ref-or-missing-guard definition): none.

**Tests:** new `tests/Unit/ConfigRequestContractsTest.php` (9 rule-shape pins, no DB) + new `tests/Feature/TemplateRequestSmokeTest.php` (Chunk 1 smoke: store 201 + row persisted with `created_by`, invalid weight rejected).

**Verification:** Unit 52 green (43 + 9 new) · Feature 171 green sharded per-file (169 + 2 new) — combined run OOMs at the 128M default (pre-existing env limit; `php -d` does not propagate to the test child process, recorded for Stream 3) · GroupLifecycle 20 green · render 75/0 · `route:list` 1482 clean · `BladeFinalizationTest` identical env failure (14 failed / 0 assertions) · `pint` clean.

## Stream 2 Chunk 2 — Scheduling/rooms Requests (this session, no commits)

Eight inline validations → 7 Form Requests in new `Requests/Scheduling/` (no precedent dir — created): `StoreLocationRequest` + `UpdateLocationRequest` (`unique` ignore resolves `{location}` route param, verified via route:list apiResource) + `AvailableLocationsRequest` (LocationController; all three are discarded-validation shape — callers keep `$request->name` / `$request->all()` reads verbatim, including `$request->all()` NOT narrowed to validated in update) · `StoreScheduleRequest` + `UpdateScheduleRequest` (ScheduleController) · `SeminarScheduleRequest` shared by all three ScheduleRequestController sites (byte-identical rules verified before sharing; private `requestSeminar` signature changed alongside its two public callers) · `CalendarIndexRequest` (single nullable rule). Controllers show only signature swaps + deleted inline blocks.

One mechanical adaptation (logged): ScheduleController store/update embed runtime `$allowedTypes` (dosen→BIMBINGAN-only, else SEMPRO/SIDANG/EXPO) in the `type` rule. The identical derivation runs inside rules() (same Auth/CapstoneActor calls, same ternary) — rule shape moved, no authorization moved. At HTTP runtime middleware sets `capstone_role` before FormRequest validation, so the accepted set is identical; in direct-call tests the derivation falls back to `roles[0]`, which is deterministic for single-role users (verified `CapstoneActor::role`).

Stale-test repairs (my change surfaced them; Both additions): `ScheduleVisibilityTest::requestAs` + `ScheduleCancellationTest::requestAs` now build `CalendarIndexRequest` (subclass, so SeminarDashboardController call sites keep working); `BladeCalendarAccessTest` gained a generic `formRequestFor()` builder (route params bound via a bound `Route` — first attempt threw `Route is not bound`, fixed with `bind()`) and converted all Chunk 1 + Chunk 2 call sites (config tables/bank/calendar/schedule tests). All 8 touched Blade tests pass. `EofficeRoomIntegrationTest` 11 failed / 1 passed is pre-existing (verified identical via stash: EOffice-module `eo_mr_peminjamans.deleted_at` schema gap, untouched by this stream).

FOUND-NOT-FIXED: none. Smelly (wrong-ref-or-missing-guard definition): none.

**Tests:** new `tests/Unit/SchedulingRequestContractsTest.php` (8 pins incl. both dosen + admin role branches via `be()` with relation-only users, no DB) + new `tests/Feature/LocationRequestSmokeTest.php` (Chunk 2 smoke: store 201 + row persisted, non-admin 403, invalid type rejected — note: rejection code is 403 from `unauthorizedResponse`, first pinned wrong as 401 and corrected).

**Verification:** Unit 60 green (52 + 8 new) · Feature 174 green sharded per-file (171 + 3 new; combined run still OOMs at 128M default — env, see Chunk 1) · `BladeCalendarAccessTest` 49 passed / 27 failed with zero failures referencing converted controllers (remaining: pre-existing GradeReport/ReportDetail/SupervisorEval-DI/Blade-HTML/DocumentController-ctor set) · render 75/0 · `route:list` 1482 clean · `BladeFinalizationTest` identical env failure (14 failed / 0 assertions) · `pint` clean.

## Stream 2 Chunk 3 — Titles/bids Requests (this session, no commits)

Ten inline validations → 11 Form Requests: `Requests/Title/StoreTitleRequest` + `UpdateTitleRequest` (kept the defensive `unset($validated['period_id'])` line verbatim — `prohibited` already guarantees absence, so it stays an exact no-op) · `StoreBidRequest` + `ReorderBidsRequest` + `RecommendBidRequest` (discarded shape) · `RejectTitleProposalRequest` · `StoreStudentProposalRequest` + `UpdateStudentProposalRequest` (the `save()` `$editing` ternary split: rules() cannot see the method argument, so store carries `prohibited`, update carries `required|…`; shared private `save()` now type-hints parent `FormRequest`) · `Requests/Period/StorePeriodRequest` + `UpdatePeriodRequest` (new topical dir — BladePeriod in Chunk 5 shares the domain; first placed under Title/, moved before gating) · `Requests/Admin/TitleManagement/UpdateTitleTextRequest` (all 12 governance `prohibited` columns verbatim). Controllers show only signature swaps + deleted inline blocks; `Illuminate\Http\Request` imports kept wherever plain-Request methods remain.

Caught during work (own mistake, fixed): replacing the shared Request import in StudentProposalController briefly broke `myProposal`/`destroy` (resolved to a nonexistent namespaced `Request`) — restored; audit of all six controllers confirms import-present-iff-plain-Request-used.

Stale-test repairs: Blade `solo-proposal`/`bid-reorder`/`rejected-bid` tests converted to `formRequestFor()` builders. Side effect: the solo-proposal test now passes — it was red on baseline because `new StudentProposalController` (0 args) predates the DI ctor; switched to `app(...)` container resolution (trivial fix, within the fix-trivial Stream 4 allowance, done early because Chunk 3 needed the test green).

FOUND-NOT-FIXED: none. Smelly (wrong-ref-or-missing-guard definition): none.

**Tests:** new `tests/Unit/TitleRequestContractsTest.php` (11 pins incl. all 12 governance `prohibited` columns loop-asserted) + new `tests/Feature/TitleRequestSmokeTest.php` (Chunk 3 smoke: store 201 + LECTURER source persisted, missing title rejected).

**Verification:** Unit 71 green (60 + 11 new) · Feature 176 green sharded per-file (174 + 2 new) · `BladeCalendarAccessTest` 50 passed / 26 failed — baseline was 49/27, delta is exactly the repaired solo-proposal test, zero failures reference converted controllers · render 75/0 · `route:list` 1482 clean · `BladeFinalizationTest` identical env failure (14 failed / 0 assertions) · `pint` clean.

## Stream 2 Chunk 4 — Evals/registrations Requests (this session, no commits)

Ten inline validations → 10 Form Requests: `Requests/Evaluation/StoreEvaluationRequest` (keeps the `Rule::in` object; pin asserts via string form) · `Requests/Expo/StoreExpoEventRequest` + `UpdateExpoEventRequest` · `Requests/Admin/TaRegistration/RejectTaRegistrationRequest` + `Requests/Admin/PeriodRegistration/RejectPeriodRegistrationRequest` (identical rules, separate classes per mirror rule) · `Requests/Admin/Stakeholder/StoreStakeholderRequest` + `UpdateStakeholderRequest` + `AttachStakeholderRequest` · `Requests/DigitalSignature/SignDocumentRequest` (new topical dir). Private `normalizePeriodInput(Request)` signatures untouched (FormRequest is a subclass). Controllers show only signature swaps + deleted inline blocks.

Behavior-preservation catch (the one real trap this stream): ExpoEvent store/update call `normalizePeriodInput()` BEFORE `validate()`, so validation sees the normalized NULL — but FormRequest validation runs before the controller body, on the RAW 0/'' input, which would fail `exists`. Fixed via `prepareForValidation()` replicating the normalize verbatim in both Expo Requests (the canonical Laravel seam; controller keeps its now-no-op normalize call). Proven by the pre-existing `ExpoCrossPeriodTest` characterization (zero/empty-string period_id → global event), which failed mid-chunk and passes after.

Stale-test repairs: `ExpoCrossPeriodTest::writeRequest` now builds Store/UpdateExpoEventRequest (PUT→update); Blade `ta_registration_approval_gates_document_upload` reject call converted via `formRequestFor()`. `approve()` call sites untouched (unconverted methods).

FOUND-NOT-FIXED: none. Smelly (wrong-ref-or-missing-guard definition): none.

**Tests:** new `tests/Unit/EvalRequestContractsTest.php` (9 pins) + new `tests/Feature/SignatureRequestSmokeTest.php` (Chunk 4 smoke: sign 201 + hash persisted, unknown document_type rejected).

**Verification:** Unit 80 green (71 + 9 new) · Feature 178 green sharded per-file (176 + 2 new) · `BladeCalendarAccessTest` 50 passed / 26 failed, zero failures referencing converted controllers (remaining: pre-existing set incl. SupervisorEvaluation/DocumentController ctor staleness) · render 75/0 · `route:list` 1482 clean · `BladeFinalizationTest` identical env failure (14 failed / 0 assertions) · `pint` clean.

## Stream 2 Chunk 5a — Reports/files/misc Requests (this session, no commits)

Seventeen inline validations → 14 Form Requests: `Requests/Report/SummaryRequest` + `ExportRequest` (new dir) · `Requests/File/UploadFileRequest` (array-form rules kept) · `Requests/Registration/RegisterPeriodRequest` (new dir) · `Requests/Auth/ExchangeTokenRequest` + `SetActiveRoleRequest` (new dir) · `Requests/Grade/GradeConsistencyQueryRequest` (shared by index + generate, byte-identical rules verified before sharing) + `UpdateConsistencyCheckRequest` · `Requests/Admin/User/IndexUsersRequest` (new dir) · `Requests/Admin/Monitoring/ProgressQueryRequest` + `PeerReviewsQueryRequest` (identical single rules, separate classes per mirror rule) + `MessageGroupRequest` + `FlagMemberRequest` + `DestroyGroupRequest`. All discarded-validation sites keep downstream `$request->…` / `$request->filled/integer` / `$request->all()` reads verbatim. Controllers show only signature swaps + deleted inline blocks.

Import discipline (two near-misses caught by audit): FileController briefly lost its Request import while download/show/list/delete still need it (restored); GradeConsistencyController briefly lost its model import instead of the unused Request import (repaired immediately). Rule going forward: grep remaining `(Request $request` before removing any Request import.

Stale-test repairs: Blade monitoring `peerReviews`/`progress` + registration `register` call sites converted via `formRequestFor()` (null-user variant for user-less feeds). CapstoneSsoTest untouched (only `me()`, unconverted).

FOUND-NOT-FIXED: none. Smelly (wrong-ref-or-missing-guard definition): none.

**Tests:** new `tests/Unit/MiscRequestContractsTest.php` (13 pins) + new `tests/Feature/ConsistencyRequestSmokeTest.php` (Chunk 5a smoke: update persists status + checker, unknown status rejected).

**Verification:** Unit 93 green (80 + 13 new) · Feature 180 green sharded per-file (178 + 2 new) · `BladeCalendarAccessTest` 50 passed / 26 failed, zero failures referencing converted controllers · render 75/0 · `route:list` 1482 clean · `BladeFinalizationTest` identical env failure (14 failed / 0 assertions) · `pint` clean.

## Stream 2 Chunk 5b — Heavy controllers Requests (this session, no commits)

~52 inline validations → 25 Form Requests: `Requests/Sempro/` (5: Update/Schedule/Evaluate/Approve/Reject) · `Requests/TaDefense/` (4: Schedule/Evaluate/Approve/Reject) · `Requests/TaSubmission/` (2: shared `TaSubmissionFileRequest` for byte-identical upload+revise, `ReviewTaSubmissionRequest`) · `Requests/Expo/` (+3: SubmitExpoScores/UploadExpoDocument/RejectExpo) · `Requests/PeerReview/` (5: Store/GroupReviews/Indicators/StoreIndicator/UpdateIndicator) · `Requests/BursaIde/RequestJoinRequest` + `Requests/IndividualTa/UploadTaDocumentRequest` (new dirs) · `Requests/Period/SavePeriodRequest` (shape-5 shared saver: loops over `BladePeriodController::TYPES` moved into rules(), store/update/save signatures changed) · `Requests/SupervisorEvaluation/AdminScheduleSummaryRequest` (topical, controller is not under Admin/) · `Requests/Assessment/StorePeerReviewConfigRequest` + `CopyPeerReviewConfigRequest` (not_in route-param adaptation, same as Chunk 1 copy). Controllers show only signature swaps + deleted inline blocks.

Saved-storage rule patched mid-chunk: IndividualTa/ExpoStudent tests set `$request->files` AFTER building — FormRequest validates at build, so the `formRequestFor()` helper gained a `$files` parameter set before `validateResolved()`. Merge-reuse pattern in PeerReview tests (`$request->merge()` then re-call) replaced with fresh builds per call: `validated()` snapshots at validation time, so re-calling with merged input would silently use stale data — fresh builds match HTTP semantics exactly.

Stale-test repairs: ~15 Blade call sites converted (Sempro update, peer-review config store/copy, BladePeriod save, PeerReview stores, IndividualTa uploads, TaSubmission legacy, ExpoStudent evaluate/document). `test_expo_rejects_wrong_components` was a live regression caught by the gate (TypeError) and fixed; the final 26 Blade failures are byte-identical to the pre-existing set (verified by name via --testdox: GradeReport/ReportDetail/SupervisorEval-DI/DocumentController-DI/DocumentUpload/manifest/Blade-HTML).

FOUND-NOT-FOUND: none. Smelly (wrong-ref-or-missing-guard definition): none. New dead-surface note: none (all Chunk-5b controllers are routed).

**Tests:** new `tests/Unit/HeavyRequestContractsTest.php` (25 pins incl. TYPES-loop expansion assertions for the saver) + new `tests/Feature/IndicatorRequestSmokeTest.php` (Chunk 5b smoke: indicator create 201 + row persisted, missing name rejected).

**Verification:** Unit 118 green (93 + 25 new) · Feature 182 green sharded per-file (180 + 2 new) · `BladeCalendarAccessTest` 50 passed / 26 failed with zero failures referencing converted controllers · render 75/0 · `route:list` 1482 clean · `BladeFinalizationTest` identical env failure (14 failed / 0 assertions) · `pint` clean.

## Stream 3 — Batch H env reds diagnosed (this session, no commits, no app-code changes)

Root cause captured (shared by all three suites — verified, not assumed): `Modules/ManajemenMahasiswa/database/migrations/2026_09_04_000001_create_mk_kegiatan_dosen_pendamping_table.php:52` runs `dropColumn('dosen_pendamping_id')` on `mk_kegiatan`, which SQLite rejects (`unknown column in foreign key definition`; the migration guards only `dropForeign` for non-sqlite). All three suites use `RefreshDatabase`, which runs every app migration on sqlite `:memory:`: `BladeFinalizationTest` 14 failed / 0 assertions, `BladeUsersTest` 9 failed / 0 assertions, `AdminTitleTextUpdateTest` 2 failed / 0 assertions — identical SQLSTATE signature in each. Offender is outside Capstone → NO drive-by fix. Proposal for the owning module: skip/rebuild that drop on sqlite (guard `DB::getDriverName()`, or table-rebuild pattern), or exclude the migration from test refresh.

OOM ladder, measured: combined Capstone Feature run peaks at 132.50 MB vs the 128M default CLI limit (`artisan test` drops parent `php -d` flags in its child process — verified via tinker inheriting 512M while test children stay at 128M). Direct `php -d memory_limit=512M vendor/bin/phpunit Modules/Capstone/tests/Feature` → OK (182 tests, 535 assertions). No leak, no app bug — environmental only. Working gate stays sharded per-file (all 20 green); no config changed without approval.

## Stream 4 — Whole-suite triage (this session, no commits, triage only)

PHP (Capstone scope): Unit 118 green · Feature 182 green (combined at 512M) · render 75/0 · `BladeCalendarAccessTest` 50 passed / 26 failed — named via --testdox, all pre-existing, bucketed below · root red suites all share the Stream 3 migration cause (`BladeFinalization` 14, `BladeUsers` 9, `AdminTitle` 2, `GroupMessageFlag` 4) · `EofficeRoomIntegrationTest` 11 failed / 1 passed — EOffice-module schema gap (`eo_mr_peminjamans.deleted_at` missing in test schema), proposal for that module · `ScheduleDashboardDesignTest` 7 failed — EN-vs-ID copy drift, product call on which copy is canonical.

Blade-26 bucket (fix-trivial allowance applied, ~30 min rule): NONE fixed inline — the ctor-staleness cluster needs 5-deep service graphs (`SupervisorEvaluationController` ← SupervisorEvaluationService ← lifecycle/presenter/grades/notifications; `DocumentController` ← DocumentStorageService ← SupabaseStorage + 4 services), exceeding trivial; product-behavior gaps (`GradeReport::evaluatorDetail`, `ReportDetailController::applyPeerReviewSorting`, `DocumentUploadController::getExpoDocuments` — all called but never defined, likely missed in earlier extractions) need behavior spec; copy/HTML drifts need product call. Returned as proposals:
1. Wire ctor-stale Blade tests via `app()` container (pattern already proven: solo-proposal test fixed in Chunk 3) or construct fakes for the service graphs.
2. Specify-then-implement the three missing methods (evaluatorDetail, applyPeerReviewSorting, getExpoDocuments).
3. Rule on EN-vs-ID dashboard/report copy.

JS (triaged, not fixed per plan): `test:ui` 56 pass / 3 fail (allocation ids, TA approvals endpoint, admin TA edits) · excluded 4 suites 17 pass / 6 fail (examiner scores, role selection, users list ×3, dashboard-stepper set) — all frontend-only, untouched by this sweep, returned as proposals for the frontend owner.

**Final gate:** Unit 118 · Feature 182 · render 75/0 · `route:list` 1482 · `pint` clean · all remaining reds bucketed above with owners.
