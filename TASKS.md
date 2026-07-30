# Tournatak Cleanup & De-duplication Plan

**Baseline:** 211 tests passing (385 assertions), Pint clean, 91 routes, 0 composer/npm vulnerabilities.

---

## Phase 0 — Safe Improvements (no behavior changes)

### 0.2 `ScoringEngine` head_to_head tiebreaker no-op
- **File:** `app/Services/ScoringEngine.php:67`
- **Issue:** `'head_to_head' => 0` returns `0` (no-op), falls through to next tiebreaker. This means head_to_head is silently ignored.
- **Fix:** Either (a) implement proper H2H comparison with match data, or (b) comment/remove from default tiebreakers so admin doesn't expect it to work. Option (b) is safer for Phase 0.

### 0.3 Add PHPStan configuration
- **Files:** `phpstan.neon` (missing)
- **Issue:** No static analysis configured.
- **Fix:** Create `phpstan.neon` at level 1 (relaxed) with Larastan extension stubs.

### 0.4 Enhance CI with audit steps
- **File:** `.github/workflows/ci.yml`
- **Issue:** CI runs Pint and tests but no `composer audit` or `npm audit`.
- **Fix:** Add audit steps after install.

### 0.5 JS duplication: `confirmDelete` vs `confirmSweetAlert`
- **Files:**
  - `resources/js/app.js:78-94` — `confirmDelete()` with hardcoded Arabic strings
  - `resources/views/components/delete-confirm-button.blade.php` — `confirmSweetAlert()` with translated strings
- **Issue:** Two different global delete-confirmation functions with overlapping purpose.
- **Fix:** Remove `confirmDelete()` from `app.js`; enhance `delete-confirm-button` component to be usable as a drop-in replacement.

### 0.6 CompetitionService missing DB transaction
- **File:** `app/Services/CompetitionService.php`
- **Issue:** `create()` does not wrap operations in `DB::transaction()`.
- **Fix:** Wrap in `DB::transaction()`.

---

## Phase 1 — De-duplication

### 1.1 Extract RegistrationService
- **Files with duplicated registration logic:**
  - `app/Livewire/Admin/Registrations/CreateRegistrationPage.php` — individual registration
  - `app/Livewire/Admin/Registrations/CreateTeamRegistrationPage.php` — team registration
  - `app/Livewire/User/RegistrationsPage.php` — user-facing registration listing
- **Plan:** Create `app/Services/RegistrationService.php` with methods:
  - `createRegistration(array $data): Registration`
  - `createTeamRegistration(array $data): Registration`
  - `getUserRegistrations(User $user): Collection`
- Also merge the two nearly-identical Blade views:
  - `resources/views/livewire/admin/registrations/create-registration-page.blade.php`
  - `resources/views/livewire/admin/registrations/create-team-registration-page.blade.php`
  - These are ~85% identical (same layout, card, button patterns; differ only in search model/fields).
- Extract into a shared partial or parameterized component.

### 1.2 Move MatchControlPage phase transitions to MatchService
- **File:** `app/Livewire/Admin/Matches/MatchControlPage.php:78-174`
- **Issue:** 8 phase-transition methods (`startFirstHalf`, `endFirstHalf`, `startSecondHalf`, `endSecondHalf`, `startETFirstHalf`, `endETFirstHalf`, `startETSecondHalf`, `endMatch`) all contain inline `$this->match->update(...)` logic.
- **Fix:** Create `app/Services/MatchService.php` with methods:
  - `transitionPhase(Match_ $match, string $phase): void`
  - `updateScore(Match_ $match, int $score1, int $score2): void`
  - `addEvent(Match_ $match, array $eventData): MatchEvent`
- Move all `extra_data` phase manipulation, status updates, and score persistence into the service.
- Also move `computeCurrentMinute()` — this is domain logic, not view logic.

### 1.3 Fix EditCompetitionPage to use CompetitionService
- **File:** `app/Livewire/Admin/Competitions/EditCompetitionPage.php`
- **Issue:** Edit/save logic bypasses `CompetitionService` and directly calls `$this->competition->update(...)`.
- **Fix:** Delegate to `CompetitionService::updateCompetition()`.

### 1.4 Create Blade badge/status-pill component
- **Issue:** ~173 `badge` occurrences across 47 view files, all inline `<span class="badge bg-*">`. No consistent badge component.
- **Fix:** Create `resources/views/components/status-badge.blade.php`:
  ```blade
  <span {{ $attributes->class(['badge', 'bg-' . ($variant ?? 'secondary')]) }}>
      {{ $slot }}
  </span>
  ```
- Refactor top-10 most badge-heavy files first (low-hanging fruit):
  - `dashboard-page.blade.php` (12)
  - `lineup-page.blade.php` (10)
  - `competition-detail-page.blade.php` (10)
  - `match-control-page.blade.php` (8)
  - `registrations-page.blade.php` (user, 8)

### 1.5 Extract participant-type badge into component
- **Files:**
  - `resources/views/livewire/admin/competitions/create-competition-page.blade.php:35-47`
  - `resources/views/livewire/admin/competitions/edit-competition-page.blade.php:35-47`
- **Issue:** Identical 12-line block duplicated in both create and edit views.
- **Fix:** Extract into `resources/views/components/participant-type-badge.blade.php`.

### 1.6 Duplicated Alpine.js timer logic
- **Files:**
  - `resources/views/livewire/admin/matches/match-control-page.blade.php`
  - `resources/views/livewire/admin/matches/matches-page.blade.php`
- **Issue:** Both files implement near-identical match clock logic using Alpine.js `x-data`/`x-init`.
- **Fix:** Extract into a shared Alpine.js component or a dedicated Blade component.

### 1.7 9 create/edit component pairs — extract shared logic
- **9 pairs:**
  | Domain | Create | Edit |
  |--------|--------|------|
  | Users | `Admin/Users/CreateUserPage.php` | `Admin/Users/EditUserPage.php` |
  | Subtypes | `Admin/Subtypes/CreateSubtypePage.php` | `Admin/Subtypes/EditSubtypePage.php` |
  | Types | `Admin/Types/CreateTypePage.php` | `Admin/Types/EditTypePage.php` |
  | Sports | `Admin/Sports/CreateSportPage.php` | `Admin/Sports/EditSportPage.php` |
  | Referees | `Admin/Referees/CreateRefereePage.php` | `Admin/Referees/EditRefereePage.php` |
  | Teams | `Admin/Teams/CreateTeamPage.php` | `Admin/Teams/EditTeamPage.php` |
  | Matches | `Admin/Matches/CreateMatchPage.php` | `Admin/Matches/EditMatchPage.php` |
  | Competitions | `Admin/Competitions/CreateCompetitionPage.php` | `Admin/Competitions/EditCompetitionPage.php` |
  | Players | `Admin/Players/CreatePlayerPage.php` | `Admin/Players/EditPlayerPage.php` |
- **Issue:** Each pair has ~60-70% code duplication in both PHP logic and Blade views.
- **Plan:** For each pair, extract shared logic into a trait or base class. Then merge Blade views into a single parameterized view.

---

## Phase 2 — Componentization

### 2.1 Create reusable Blade components
- `status-badge` — mapped from `badge bg-*` inline patterns (P1.4, but full sweep)
- `confirm-delete` — enhance existing `delete-confirm-button` to accept custom messages
- `section-header` — breadcrumb + title + action button pattern (repeated ~25 times)
- `admin-table` — standardized data table with search/pagination (repeated ~15 times)

### 2.2 Inline JS → extracted app.js
- **Files with inline `<script>` (7):**
  - `layouts/app.blade.php` (4 script blocks)
  - `layouts/admin.blade.php` (3)
  - `notification-bell.blade.php` (1)
  - `user-preferences-page.blade.php` (1)
  - `lineup-page.blade.php` (1 — Alpine component)
  - `delete-confirm-button.blade.php` (1 — `confirmSweetAlert`)
  - `dashboard-page.blade.php` (1)
- **Plan:** Move all non-critical inline scripts into `app.js` or dedicated JS modules. Keep only init hooks inline.

### 2.3 Remove/refactor legacy `pages/` directory
- **Files:** `resources/views/pages/competitions.blade.php`, `players.blade.php`, `teams.blade.php`
- **Issue:** These are pre-Livewire static Blade pages. They may be dead code.
- **Action:** Check for route references. If unused, delete. If still referenced, add deprecation notice and plan migration.

### 2.4 Synchronize translation files
- **Current counts:** en=1318, ar=1317, fr=1313, es=1285
- **Issue:** ES lags 33 keys behind en (and FR lags 5, AR lags 1). Missing keys cause fallback to key name.
- **Fix:** Add missing keys to ES, FR, and AR locales. Verify using `php artisan translations:check` (or manual diff).

---

## Phase 3 — Data Consistency & Schema Hardening

### 3.1 Consolidate hardcoded status strings into enums
- **Issue:** Status strings (`'scheduled'`, `'in_progress'`, `'completed'`, `'draft'`, `'upcoming'`, `'ongoing'`) are hardcoded in 13+ Livewire components.
- **Fix:** Create PHP enums:
  - `App\Enums\MatchStatus` (scheduled, in_progress, completed, cancelled, postponed, abandoned, pending)
  - `App\Enums\CompetitionStatus` (draft, upcoming, ongoing, completed)
  - `App\Enums\ApprovalStatus` (pending, approved, rejected)
- Refactor all Livewire components, models, and services to use enums instead of string literals. (Non-breaking: enum-backed strings are compatible with existing DB values.)

### 3.2 Fix migration SQLite guards
- **Files:**
  - `2026_07_28_233208_update_matches_status_enum.php` — guarded to MySQL only
  - `2026_07_30_170932_fix_audit_issues.php` — enum revert guarded
  - `2026_07_30_192000_add_individual_participant_support.php` — try/catch + guards
- **Issue:** These guards cause tests on SQLite to silently skip schema changes, leading to test/env mismatch.
- **Fix:** Use `\Doctrine\DBAL\Schema\AbstractAsset::addOption()` pattern or restructure to avoid DBAL enum changes on SQLite. Consider using string columns instead of enums to avoid the problem entirely.

### 3.3 Add factories for remaining models
- **21 models missing factory files:**
  Activity, Formation, MatchLineup, MatchStat, News, Plan, PlayerSeasonStat, Position, Profile, Referee, SecuritySetting, Sport, Subscription, TeamMedicalRecord, TeamSeasonStat, TeamStaff, TeamTactic, TwoFactorRecoveryCode, UserNotification, UserPreference, Verification
- **Priority:** Only create factories for models used in tests (low priority if not currently tested).

### 3.4 CompetitionSubtype `$casts`
- Already in Phase 0 (P0.1) — add `$casts` property.

---

## Phase 4 — Testing & CI Hardening

### 4.1 PHPStan setup & baseline
- Create config, run at level 1, fix initial errors, integrate into CI.

### 4.2 Add tests for RegistrationService
- Test both individual and team registration flows.
- Test validation edge cases.

### 4.3 Add tests for MatchService phase transitions
- Test all 8 phase transitions.
- Test score persistence.
- Test added-time persistence.

### 4.4 Add tests for ScoringEngine tiebreakers
- Test each tiebreaker rule (goal_difference, goals_for, goals_against, wins).
- Test head_to_head (once implemented) or document the gap.

### 4.5 Add `composer audit` and `npm audit` to CI
- Run after dependency install in CI workflow.

### 4.6 Add test coverage for registration approval flows
- Cover approval status transitions (pending → approved/rejected).

---

## Deferred (not in scope for this pass)

| Item | Reason |
|------|--------|
| Policy gaps on Team/Registration/Player models | Behavioral change; needs requirements clarity |
| SportConfigService fully replaced by model accessors | Low impact; adds no cleanup value |
| ScoringEngine full H2H implementation | Needs match data design; complex |
| `lineup-page.blade.php` refactor (576 lines) | Stable, low duplication risk |
| `match-control-page.blade.php` refactor (537 lines) | Will refactor in P1.2 via MatchService |
| Remove unused CSS/JS from vendor bundles | Risk of breaking existing functionality |
| Docker/local development environment improvements | Out of scope |
| Performance optimization (N+1 queries, lazy loading) | Not detected as systemic issue |
| Model `$fillable` audit for mass-assignment safety | No known vulnerability; pre-existing pattern |

---

## Execution Order

```
Phase 0 ──→ Phase 1 ──→ Phase 2 ──→ Phase 3 ──→ Phase 4
(safe)      (dedup)     (comps)     (schema)    (quality)
```

Each phase must pass `vendor/bin/pint --test && php artisan test` before the next phase begins. No phase depends on a later phase.

**Phase 0 tasks are independent** and can be parallelized. **Phase 1 must precede Phase 2** because de-duplication may create new components naturally.
