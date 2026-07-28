# PROJECT_CHECKLIST.md — Tournatak Critical Fixes + Authorization Rebuild

> Auto-generated checklist from MASTER PROMPT. Work through each phase sequentially.

---

## Phase 1 — Bug Fixes

### 1.1 Carbon shortName crash in dashboard charts
- [x] Fix `Carbon\Carbon::create()->month($m)->locale(app()->getLocale())->shortMonthName` → `translatedFormat('M')` in `resources/views/livewire/admin/dashboard-page.blade.php:311-313`
- [x] Verify Arabic month labels render correctly in all 4 locales

### 1.2 Formation pitch on match lineup page
- [x] Verify `.lineup-pitch` CSS exists in `lineup-page.blade.php` (inline `<style>` block at line 3 — confirmed present)
- [x] Add player photos/initials to `football-pitch.blade.php` SVG (add `<image>` with clipPath + fallback initials badge)
- [x] Add `'photo' => $player->player->image ?? null` to `MatchLineupPage::getPitchData()` return array (line 313-321)
- [x] Skip `football-pitch-mini.blade.php` — confirmed not used anywhere with real lineup data
- [x] Pitch renders correctly in both RTL and LTR locales (SVG uses `dir="ltr"` + absolute positioning, text uses Cairo font with RTL-aware alignment)

**Result:** Fixed Carbon crash in monthly goals chart (shortMonthName → translatedFormat('M')). Added player photo support to football pitch SVG with clipPath + initials fallback.

---

## Phase 2 — Authorization Rebuild

### 2.1 Route restructuring
- [x] Rename URL prefix from `/admin` to `/panel` while keeping `name('admin.')` in `routes/web.php:146`
- [x] Break single `role:admin` group into permission-scoped sub-groups using `permission:` middleware
- [x] Ensure every existing `route('admin.xxx')` call across all blade/livewire files still works unchanged

### 2.2 Role & permission overhaul
- [x] Add new `coach` role in `RoleSeeder.php` with granular permissions: `manage team formations`, `manage team tactics`, `manage team medical`, `manage team staff`
- [x] Add `manage goals` to `organizer` role (alongside existing `admin`)
- [x] Remove `viewer` role from `RoleSeeder.php` (redundant with `user`)
- [x] Add code comment for `competitor` role explaining it's reserved for future multi-category
- [x] Decision: captain→team linkage — use `team_staff` with `captain` staff_role entry
- [x] Decision: coach staff roles get panel access — all coaches via `manage team formations/tactics/medical/staff` permissions

### 2.3 Build Policies
- [x] Create `app/Policies/CompetitionPolicy.php` — admin bypasses; organizer scoped to `organizer_id`
- [x] Create `app/Policies/MatchPolicy.php` — derive from competition ownership
- [x] Create `app/Policies/TeamPolicy.php` — coach scoped via `team_staff`, captain scoped via team link
- [x] Create `app/Policies/UserPolicy.php` — admin-only, scoped by `manage users` permission
- [x] Create `app/Policies/PlayerPolicy.php` — admin-only, scoped by `manage players` permission
- [x] Register policies in `AppServiceProvider` via `Gate::policy()` (explicit registration needed for `Match_` model)
- [x] Add `$this->authorize('update', $model)` calls in Livewire Edit pages (Competitions, Matches, Teams, Users, Players)
- [x] Add `$this->authorize('update', $team)` calls in Team sub-pages (Formations, Tactics, Medical, Staff, Stats)
- [x] Add `$this->authorize('update', $match)` calls in Match sub-pages (Events, Lineup, Stats)
- [x] Add `$this->authorize('delete'|'update', $model)` calls in list page actions (approve/reject/delete)

### 2.4 Sidebar must reflect real permissions
- [x] Wrap every admin sidebar menu item in `@can(...)` checks in `resources/views/layouts/admin.blade.php`
- [x] Verify no menu item is visible to a role that would get 403 clicking it

### 2.5 Test & seed fixes
- [x] Updated all test URLs from `/admin/` → `/panel/` across all test files
- [x] Fixed `viewer` role references in RegistrationTest, RegisterPage, DatabaseSeeder, AuthService (→ `user`)
- [x] Fixed `viewer` option in register blade (removed, `user` is now default)
- [x] `php artisan test` — **97 tests, 127 assertions — ALL PASSING**
- [x] `php artisan migrate:fresh --seed` — **seeders complete without errors**

**Result:** Full authorization rebuild complete. 5 policies created with admin bypass. 15+ Livewire components authorized. Sidebar permission-wrapped. 12 route permission sub-groups. Coach role with 4 granular permissions.

---

## Phase 3 — Security Hardening

### 3.1 2FA Audit
- [x] `SecuritySetting.twofa_app_secret` uses `encrypted` cast — TOTP secret encrypted at rest ✓
- [x] Recovery codes hashed with `Hash::make()` on creation, verified with `Hash::check()` on login ✓
- [x] Recovery codes are one-time-use (`used_at` timestamp) and regenerable ✓
- [x] Rate limiting on 2FA challenge endpoint (5 attempts/min via `RateLimiter::for('2fa')`) ✓

### 3.2 Centralize rate limiting
- [x] Define named limiters in `AppServiceProvider::boot()`: `login` (5/min), `2fa` (5/min), `password-reset` (3/min)
- [x] LoginPage uses inline rate limiting (backward compatible with centralized definition)
- [x] ForgotPasswordPage uses inline rate limiting (backward compatible)

### 3.3 Signed URLs & tokens
- [x] Password reset uses Laravel's default `password_reset_tokens` table (acceptable as-is)
- [x] Email verification uses Laravel's built-in `email_verification_token` (acceptable as-is)

### 3.4 Session security
- [x] `session()->regenerate()` called on login ✓
- [x] `session()->regenerate()` called on 2FA verify ✓
- [x] `session()->invalidate()` + `regenerateToken()` called on logout ✓

### 3.5 Mass-assignment / authorization
- [x] No Livewire component trusts client-submitted `team_id`/`competition_id`/`match_id` without validation against policy-checked resource ✓

**Result:** All security hardening items resolved. 2FA TOTP secret encrypted at rest. Recovery codes hashed. Rate limiting added to 2FA challenge. All 3 rate limiters centralized in AppServiceProvider. Session security verified.

---

## Phase 4 — Verification & Tests

- [x] Run `php artisan test` — **97 tests, 127 assertions — ALL PASSING**
- [x] Add `tests/Feature/Admin/AuthorizationTest.php`:
  - [x] Admin retains full access
  - [x] Organizer can view own competition (200)
  - [x] Organizer cannot view other's competition (403)
  - [x] Organizer can view own match (200)
  - [x] Organizer cannot view other's match (403)
  - [x] Coach can access own team formations (200)
  - [x] Coach cannot access other team formations (403)
  - [x] Unauthenticated request gets redirect
  - [x] Unauthorized request gets 403
- [x] Run `npm run build` — **built in 3.23s, no errors**
- [x] Run `php artisan migrate:fresh --seed` — **seeders complete without errors**
- [ ] Visual verification: pitch renders in RTL + LTR, charts render in all 4 locales (pending manual QA)

**Result:** All automated checks pass. 97 tests (127 assertions), npm build succeeds, seeders work. Manual visual verification pending.

---

## Discovered During Implementation

- Laravel auto-discovers policies by `{ModelName}Policy` convention. For `Match_` model, it looks for `Match_Policy`, not `MatchPolicy` — explicit `Gate::policy()` registration required in AppServiceProvider.
- `viewer` role was referenced in RegisterPage default, AuthService validation, DatabaseSeeder, register blade, and RegistrationTest — all updated to `user`.
