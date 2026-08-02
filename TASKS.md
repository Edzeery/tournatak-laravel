# Toast & Confirmation UX (SweetAlert2) — Task Checklist

Goal: unify all error/flash feedback into a navigation-independent toast system and
replace the native `wire:confirm` dialogs with styled SweetAlert2 modals. Three phases;
each phase ends with verification and a report. Do NOT start Phase 1 until the user
confirms this plan.

## Constraints
- No new npm or composer packages (sweetalert2 `^11.26` already installed).
- All user-facing strings via `__('app.xxx')`; new keys added to all 4 locales
  `resources/lang/{en,ar,fr,es}/app.php`. Preserve RTL.
- Keep the existing dark/gold theme: bg `#1a1f35`, text `#fff`, success `#16a34a`,
  error `#ef4444`, info `#f5a622` (existing), warning gold `#f5a622`, cancel `#6b7280`.
- Do not touch: `composer.json`/`composer.lock` drift (backup 9.4.1) and the
  pre-existing uncommitted frontend edits (favicon, `_auth.scss`, layouts, auth
  blades, home-page, `public/img/`). Commit only task files.

## Audit summary (facts gathered)
- `session()->flash()` call sites: **91 across app/Livewire (51 files)**.
  - **67 NO_REDIRECT** (flash silently dropped today) → convert to `notify()`.
  - **24 redirect-following** (flash survives full page load; toast shown by layout JS) → keep.
  - Warning sites: `TwoFactorChallengePage:78` (redirect, keep flash) and
    `MatchLineupPage:426` (NO_REDIRECT, convert). Layouts have NO `warning` branch.
- Layout flash handling is duplicated in `layouts/app.blade.php` (lines ~336–399,
  success/error/info) and `layouts/admin.blade.php` (lines ~365–~400, success/error),
  each bound only to `livewire:navigated`.
- `wire:confirm`: **27 occurrences in 18 views**.
- Inline `@if($errors->any())` alert-danger blocks: **7 views**; a shared
  `x-form-errors` component already exists (`resources/views/components/form-errors.blade.php`).
- Legacy `confirmSweetAlert(url, ...)` exists at `resources/js/app.js:215` (URL-navigate
  style; used by the unused `delete-confirm-button` component and
  `two-factor-setup-page`). We add a new `confirmAction` helper; leave the legacy one.

## Verification (after each phase)
- Tests: `C:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe artisan test`
  (local default PHP 8.2.12 is blocked by the composer platform check; 8.3.28 proven: 336 tests / 7422 assertions).
- Style: `vendor/bin/pint --test` (same PHP binary prefix).
- Frontend build: `npm run build`.
- Report real output; never mark a phase complete without running these.

---

## Phase 1 — Unified toast system (`dispatch('toast')` + global listener)

### 1A. PHP helper
- [x] Create `app/Livewire/Concerns/Notifies.php` — trait with
      `notify(string $type, string $message): void { $this->dispatch('toast', type: $type, message: $message); }`
      (type in: success | error | info | warning).

### 1B. Global JS listener (resources/js/app.js)
- [x] Add `window.showToast(type, message)` — single `Swal.fire` toast (toast:true,
      position:'top-end', timer 4000/5000 per type, timerProgressBar, bg `#1a1f35`,
      color `#fff`, iconColor map success `#16a34a` / error `#ef4444` / info `#f5a622` /
      warning `#f5a622`).
- [x] Register once after `window.Swal = Swal;`:
      `Livewire.on('toast', ({ type, message }) => showToast(type, message));`
      (fires on AJAX updates AND navigations → navigation-independent).

### 1C. Layouts — backward compat for full-page-load session flashes
- [x] `resources/views/layouts/app.blade.php` (~336–399): replace the three
      `@if(session(...))`+`@push('scripts')` blocks with one block that calls
      `showToast('success'|'error'|'info'|'warning', @json(session(key)))` for each
      present key; fire on initial load AND `livewire:navigated` (avoid double-fire);
      ADD the missing `warning` branch.
- [x] `resources/views/layouts/admin.blade.php` (~365–400): same (add `info`+`warning`).

### 1D. Convert the 67 NO_REDIRECT sites (32 files) — `use Notifies;` + `session()->flash(...)` → `$this->notify(...)`
Lines refer to current `session()->flash()` calls. Errors are `error`, successes `success`, warnings `warning`.
- [x] User/ProfilePage.php:50
- [x] User/RegistrationsPage.php:38 (error), 43
- [x] Security/TwoFactorSetupPage.php:149, 174
- [x] Judge/JudgingPage.php:76
- [x] Auth/ForgotPasswordPage.php:27 (error), 42, 49
- [x] Auth/LoginPage.php:26 (error) — known dead site
- [x] Admin/Matches/CreateMatchPage.php:48 (error)
- [x] Admin/Matches/EditMatchPage.php:76 (error)
- [x] Admin/Matches/MatchLineupPage.php:256 (error), 274, 289, 324, 410 (error), 417 (error), 426 (warning), 446
- [x] Admin/Matches/MatchEventsPage.php:72 (error), 91, 104, 138
- [x] Admin/Matches/MatchStatsPage.php:165
- [x] Admin/Competitions/CompetitionsPage.php:21, 30 (error)
- [x] Admin/Competitions/CompetitionJudgingPage.php:52, 62, 71
- [x] Admin/Competitions/RoundsPage.php:62
- [x] Admin/Competitions/SubmissionsPage.php:81, 121, 135
- [x] Admin/Users/UsersPage.php:47
- [x] Admin/Positions/PositionsPage.php:139, 143, 153
- [x] Admin/Registrations/CreateRegistrationPage.php:31 (error)
- [x] Admin/Registrations/CreateTeamRegistrationPage.php:31 (error)
- [x] Admin/Registrations/RegistrationsPage.php:52, 59, 66
- [x] Admin/Types/TypesPage.php:38, 45
- [x] Admin/Subtypes/SubtypesPage.php:38
- [x] Admin/Sports/SportsPage.php:33, 40
- [x] Admin/Players/PlayersPage.php:40
- [x] Admin/TrashPage.php:71, 85
- [x] Admin/Teams/TeamFormationsPage.php:170, 176, 187
- [x] Admin/Teams/TeamsPage.php:40
- [x] Admin/Teams/TeamStaffPage.php:144, 154, 165
- [x] Admin/Teams/TeamStatsPage.php:180, 183, 218
- [x] Admin/Teams/TeamMedicalPage.php:183, 186, 197
- [x] Admin/Teams/TeamTacticsPage.php:177, 183, 194

### 1E. Keep the 24 redirect-following sites as `session()->flash(...)` (backward compat) — audit only
- [x] Confirm none of these are converted: CreateCasualCompetitionPage:54;
      CreateCompetitionPage:297; EditCompetitionPage:71; CreateMatchPage:65;
      EditMatchPage:100; CreatePlayerPage:86; EditPlayerPage:122; CreateRefereePage:56;
      EditRefereePage:73; CreateRegistrationPage:36; CreateTeamRegistrationPage:36;
      CreateSportPage:61; EditSportPage:71; CreateSubtypePage:28; EditSubtypePage:37;
      CreateTeamPage:48; EditTeamPage:107; CreateTypePage:58; EditTypePage:67;
      CreateUserPage:38; EditUserPage:53; RegisterPage:38; ResetPasswordPage:53;
      TwoFactorChallengePage:78 (warning — now renders via the new layout warning branch).

### 1F. Verify
- [x] Run tests, Pint, `npm run build`; report.

---

## Phase 2 — Unify inline validation error blocks (toast + keep per-field hints)

### 2A. Replace the 7 inline `@if($errors->any())` alert-danger blocks with `<x-form-errors />`
- [x] `livewire/auth/login-page.blade.php:52`
- [x] `livewire/auth/register-page.blade.php:62`
- [x] `livewire/auth/forgot-password-page.blade.php:37`
- [x] `livewire/auth/reset-password-page.blade.php:23`
- [x] `livewire/auth/two-factor-challenge-page.blade.php:15`
- [x] `livewire/security/two-factor-setup-page.blade.php:18`
- [x] `livewire/admin/matches/lineup-page.blade.php:447` (inside modal)
  (Preserve surrounding layout/margins where the block was not a bare component.)

### 2B. Enhance the shared component `resources/views/components/form-errors.blade.php`
- [x] Keep the alert-danger list (accessibility + inline context).
- [x] Fire a `toast` (error) when `$errors->any()` appears. NOTE: verified that Livewire 3/4
      `@script` does NOT re-run on component updates (only on load) — so instead of `@script`,
      the component uses `x-data` + `x-init` on the alert div, which runs on initial mount AND
      whenever Livewire inserts the alert after a failed validation (fires once per appearance).
- [x] Add generic key `form_validation_failed` ("Please fix the errors below") to all 4 locales.
- [x] Confirm the 18 existing `x-form-errors` usages gain the toast automatically (no regressions).

### 2C. Keep per-field inline hints untouched (auth pages show per-field help text).

### 2D. Verify
- [x] Run tests, Pint, `npm run build`; report.

---

## Phase 3 — Replace native `wire:confirm` with SweetAlert2 confirm modals

### 3A. JS helper (resources/js/app.js)
- [x] Add `window.confirmAction(options)` → returns `Promise<boolean>` (resolves true on
      confirm; false/cancel on dismiss). Options: title, text, icon ('warning'|'info'),
      confirmButtonText, cancelButtonText. Styling: bg `#1a1f35`, color `#fff`,
      confirm `#ef4444` (destructive) / `#16a34a` (start/approve), cancel `#6b7280`,
      `reverseButtons: true`, RTL-safe. Reuses existing `Swal` instance.
      Icon maps color: 'warning' → red confirm, 'info' → green confirm (overridable).

### 3B. Replace 27 `wire:confirm` occurrences (18 views) with
`x-on:click.prevent="confirmAction({...}).then(ok => ok && $wire.method(args))"`
preserving the exact method name + arguments (`$wire` is available globally via Alpine/Livewire).
Translated strings embedded via `@js()` (renders single-quoted JS literals with `\uXXXX`
escaping, safe inside double-quoted attributes; verified by rendering samples).
Method names verified against the PHP components; per-context titles/buttons reuse
`app.start_match`/`app.end_match`/`app.approve`/`app.reject`/`app.restore`.
- [x] `livewire/admin/users/users-page.blade.php:102` → `delete($user->id)`
- [x] `livewire/admin/players/players-page.blade.php:80` → `delete($player->id)`
- [x] `livewire/admin/teams/teams-page.blade.php:83` → `delete($team->id)`
- [x] `livewire/admin/positions/positions-page.blade.php:92` → `deletePosition($pos->id)`
- [x] `livewire/admin/sports/sports-page.blade.php:80` → `delete($sport->id)`
- [x] `livewire/admin/types/types-page.blade.php:90` → `delete($type->id)`
- [x] `livewire/admin/subtypes/subtypes-page.blade.php:66` → `delete($subtype->id)`
- [x] `livewire/admin/teams/team-formations-page.blade.php:86` → `deleteFormation($formation->id)`
- [x] `livewire/admin/teams/team-staff-page.blade.php:62` → `deleteStaff($member->id)`
- [x] `livewire/admin/teams/team-tactics-page.blade.php:78` → `deleteTactic($tactic->id)`
- [x] `livewire/admin/teams/team-medical-page.blade.php:166` → `deleteRecord($record->id)`
- [x] `livewire/admin/teams/team-stats-page.blade.php:207` → `deleteStat($stat->id)`
- [x] `livewire/admin/matches/events-page.blade.php:96` → `deleteEvent($event->id)`
- [x] `livewire/admin/matches/lineup-page.blade.php:305,338,384,417` → `deleteLineup($lineup->id)` (×4)
- [x] `livewire/admin/matches/match-control-page.blade.php:214,223` → `endMatch` (both sites use `endMatch`; distinct confirm texts)
- [x] `livewire/admin/matches/matches-page.blade.php:219,226,258` → `startMatch($match->id)`, `endMatch($match->id)`, `delete($match->id)`
- [x] `livewire/admin/registrations/registrations-page.blade.php:94,99,105` → `approve/reject/delete($registration->id)`
- [x] `livewire/admin/trash-page.blade.php:108,117` → `restore($type, $id)` / `forceDelete($type, $id)` (`@js($typeKeys[$typeName])` for the string arg)

### 3C. Translations
- [x] Reuse existing keys: `app.confirm_delete_yes`, `app.confirm_delete_cancel`,
      `app.confirm_delete_title`, per-item `confirm_delete_*` texts, plus
      `app.start_match`/`app.end_match`/`app.approve`/`app.reject`/`app.restore`
      as per-context titles/confirm buttons. All verified present in all 4 locales.
- [x] No new keys needed.

### 3D. Out of scope / leave as-is
- [x] `confirmSweetAlert` (app.js:266), `delete-confirm-button` component,
      `two-factor-setup-page` onclick usage — unchanged (optionally note for later).

### 3E. Verify
- [ ] Manual checks (cancel + confirm paths) on: users, teams, matches (start/end/delete),
      registrations (approve/reject), trash (restore/force-delete), lineup (deleteLineup).
- [x] `grep wire:confirm` → 0 occurrences (only comments in app.js/TASKS.md).
- [x] Run tests (336 passed / 7426 assertions), Pint (clean), `npm run build` (success).

---

## Final
- [ ] Commit only task files (not composer drift / pre-existing frontend edits) and push.
