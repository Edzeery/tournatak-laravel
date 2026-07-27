# Tournatak Platform — Professional Upgrade TODO

> Master plan for upgrading the Tournatak Laravel 12 + Livewire 4 platform into a professional, secure, scalable, multi-language sports SaaS.

---

## Phase 0 — Audit & Cleanup ✅

- [x] Audit role duplication (`users.role` string vs Spatie `HasRoles`)
- [x] Audit broken `User->activities` relationship (no `user_id` column)
- [x] Audit missing password reset flow
- [x] Audit dead-end account activation (`is_verified`)
- [x] Audit 2FA infrastructure (dead code in `security_settings` + `verifications`)
- [x] Audit CSS architecture (CDN Bootstrap, dual CSS loading, ~713 inline styles)
- [x] Audit RTL/LTR fragility (56+ ternary `getLocale() === 'ar'` checks)
- [x] Audit missing languages (no `fr/`, no `es/`)
- [x] Audit N+1 queries (DashboardPage, PlayerDetailPage)
- [x] Audit security gaps (no rate limiting, no logout auth middleware, public API)
- [x] Audit Alpine.js partially used but not loaded
- [x] Audit test coverage (zero custom tests)
- [x] Audit unused scaffolded tables (`security_settings`, `verifications`, `activities`, `notifications`, `plans`, `subscriptions`, `news`)
- [x] Audit `.env` missing keys for new features
- [x] Audit future-proofing concerns (sport-agnostic schema)

---

## Phase 1 — Database & Backend Foundations

### 1.1 Install Composer Packages
- [x] `composer require pragmarx/google2fa` (TOTP 2FA)
- [x] `composer require bacon/bacon-qr-code` (QR generation for TOTP setup)

### 1.2 Migrations (New & Modified)

#### New Migrations
- [x] **Fix `activities` table** — add `user_id` (FK), `type` (string/enum), `properties` (json nullable), `ip_address` (string nullable), `user_agent` (string nullable), `event` (string nullable)
- [x] **Create `user_preferences` table** — `user_id` (FK unique), `locale` (string, default `ar`), `theme` (enum: light/dark/system, default `system`), `timezone` (string, default `Africa/Algiers`), `date_format` (string, default `d/m/Y`), `notify_email` (bool, default true), `notify_push` (bool, default false), `sidebar_collapsed` (bool, default false), `density` (enum: comfortable/compact, default `comfortable`), timestamps
- [x] **Create `two_factor_recovery_codes` table** — `user_id` (FK), `code` (string, hashed), `used_at` (timestamp nullable), timestamps

#### Modified Migrations
- [x] Add `email_verified_at` sync column usage (decide: keep `is_verified` + sync via observer, or migrate to `email_verified_at`)

### 1.3 Models

- [x] **Create `UserPreference` model** — `belongsTo(User)`, fillable, casts
- [x] **Enhance `Activity` model** — add fillable (`user_id`, `type`, `event`, `properties`, `ip_address`, `user_agent`), add `belongsTo(User)`, add scopes (`forUser`, ` ofType`, `recent`)
- [x] **Create `TwoFactorRecoveryCode` model** — `belongsTo(User)`, `isUsed()` method, `markAsUsed()` method
- [x] **Update `User` model** — add `preference(): HasOne`, remove `'role'` from `$fillable` (Spatie is source of truth), add `email_verified_at` sync logic
- [x] **Update `SecuritySetting` model** — add `twofa_app_secret` to `$fillable` (needed for TOTP setup)

### 1.4 Observers & Sync

- [x] **Create `UserObserver`** — `created()`: assign default Spatie role from `role` column, create `UserPreference`, create `SecuritySetting`; `updated()`: if `role` column changed, `syncRoles()`; keep `role` column as denormalized cache only
- [x] **Register observer** in `AppServiceProvider::boot()`
- [x] **Remove `assignRole()` calls** from `RegisterPage.php`, `CreateUserPage.php`, `EditUserPage.php` (observer handles it)
- [x] **Remove `'role'` from `$fillable`** in `User.php` — use observer + Spatie only
- [x] **Update blade templates** that read `$user->role` to use `$user->getFirstRole()->name ?? 'user'` or cache via observer

### 1.5 Middleware

- [x] **Create `SetUserPreferences` middleware** — after auth, before bindings: read `UserPreference` for logged-in user, set `App::setLocale()`, inject `data-theme` attribute; for guests, fall back to existing cookie/session logic in `SetLocale`
- [x] **Update `SetLocale` middleware** — whitelist `['ar', 'en', 'fr', 'es']`, respect user preference if authenticated
- [x] **Register `SetUserPreferences`** in `bootstrap/app.php`
- [x] **Create `isRtl()` helper** — `app()->isLocale('ar')` or custom helper in `app/Helpers/`, use it everywhere instead of `getLocale() === 'ar'`

### 1.6 Password Reset Flow

- [x] **Create `Auth\ForgotPasswordPage`** (Livewire) — email input, sends reset link via `Password::broker()->createToken()`, branded email template
- [x] **Create `Auth\ResetPasswordPage`** (Livewire) — token validation, password + confirmation, success redirect to login
- [x] **Create `Mail\ResetPasswordMail`** (Mailable) — branded HTML email with Tournatak styling, gold accent, 4-locale support
- [x] **Add "Forgot Password?" link** to `LoginPage` blade
- [x] **Add routes** to `web.php`: `forgot-password`, `reset-password/{token}` (guest middleware)

### 1.7 Two-Factor Authentication

- [x] **Create `Auth\TwoFactorChallengePage`** (Livewire) — shows after password verification when 2FA is enabled; supports Email OTP and Authenticator App
- [x] **Create `Security\TwoFactorSetupPage`** (Livewire) — enable/disable 2FA methods, show QR code for TOTP, show recovery codes, show SMS as "coming soon"
- [x] **Create `Security\SecuritySettingsPage`** (Livewire) — view/edit all security settings, active sessions, login history
- [x] **Install `pragmarx/google2fa`** — generate secrets, verify TOTP codes
- [x] **Install `bacon/bacon-qr-code`** — generate QR code SVG for TOTP setup
- [x] **Create `Security\TwoFactorLogin`** trait/helper — shared logic for generating/verifying OTP codes via `verifications` table
- [x] **Update `LoginPage`** — after successful password check, check if 2FA is enabled; if so, store partial auth in session and redirect to `TwoFactorChallengePage`
- [x] **Rate-limit 2FA attempts** — `RateLimiter::for('2fa', ...)` — 5 attempts per 15 minutes per user
- [x] **Recovery codes** — generate 8 random codes on 2FA enable, store hashed in `two_factor_recovery_codes`, allow single-use recovery login
- [x] **Log 2FA events** — log enable/disable/success/failure as security events

### 1.8 Email Verification

- [x] **Implement `MustVerifyEmail`** on `User` model OR keep custom `is_verified` — **decision: keep `is_verified` + sync to `email_verified_at` via observer** (less migration risk)
- [x] **Create `Auth\VerificationPage`** (Livewire) — show "verify your email" page with resend button
- [x] **Create `Mail\EmailVerificationMail`** (Mailable) — branded verification email with signed URL
- [x] **Add verification routes**: `verification.verify/{id}/{hash}`, `verification.resend`
- [x] **Auto-verify on admin edit** — admin can toggle `is_verified` in `EditUserPage`

### 1.9 Security & Audit Layer

- [x] **Create `security_events` migration** OR enhance `activities` table (already planned in 1.2)
- [x] **Create `SecurityEvent` model** — `belongsTo(User)`, scopes, helper methods
- [x] **Create `SecurityEventService`** (service class) — `logLogin()`, `logLogout()`, `logPasswordChange()`, `log2faEnable()`, `log2faDisable()`, `logNewDevice()`, `logRoleChange()`
- [x] **Create `Security\SecurityLogPage`** (Livewire, admin) — filterable timeline of all security events
- [x] **Log events in auth flow** — login success/failure, logout, password reset, 2FA challenge
- [x] **Session regeneration** — confirm `session()->regenerate()` in login, `session()->invalidate()` + `regenerateToken()` in logout (already present, verify)
- [x] **Add `auth` middleware** to logout route (`web.php:84`)

### 1.10 Rate Limiting

- [x] **Add rate limiter for login** — `RateLimiter::for('login', ...)` — 5 attempts/minute per email+IP
- [x] **Add rate limiter for password reset** — `RateLimiter::for('password.reset', ...)` — 3 attempts/minute per email
- [x] **Apply throttling middleware** to login and forgot-password routes
- [x] **Show throttle feedback in UI** — "Try again in X seconds" message

### 1.11 Fix N+1 Queries

- [x] **`DashboardPage.php:42`** — add `'player.team'` to eager load (currently only `'player.user'`)
- [x] **`PlayerDetailPage.php:22`** — change `'goals.match'` to `'goals.match.team1', 'goals.match.team2'`

### 1.12 Update `.env.example`

- [x] Add `MAIL_MAILER=smtp` (document all mail config options)
- [x] Add `SESSION_SECURE_COOKIE=true` (production)
- [x] Add `SANCTUM_STATEFUL_DOMAINS` (future API)
- [x] Add `APP_DEBUG=false` (production note)
- [x] Add `GOOGLE2FA_SECRET` or config key for 2FA

### 1.13 Update User CRUD Components

- [x] **`RegisterPage.php`** — remove `'role' => $this->role` from create array, remove `$user->assignRole()` (observer handles both)
- [x] **`CreateUserPage.php`** — remove manual `assignRole()`, let observer handle
- [x] **`EditUserPage.php`** — remove manual `syncRoles()`, update `role` column and observer will sync
- [x] **`UsersPage.php:51`** — change `->where('role', ...)` filter to use Spatie `->role(...)` scope

### 1.14 Update Blade Templates

- [x] **`admin.blade.php:173`** — change `Auth::user()->role ?? 'admin'` to `Auth::user()->getFirstRole()->name ?? 'admin'`
- [x] **`users-page.blade.php:83`** — change `$user->role` to role from Spatie
- [x] **`profile-page.blade.php:54`** — change `$user->role` to role from Spatie
- [x] **`user-dashboard-page.blade.php:94`** — change `$user->role` to role from Spatie

### 1.15 Migrations to Run
```bash
php artisan migrate
```

### 1.16 New `.env` Keys Required
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tournatak.com
MAIL_FROM_NAME="${APP_NAME}"

SESSION_SECURE_COOKIE=true

# 2FA
GOOGLE2FA_ENABLED=true
```

### 1.17 Manual Steps
```bash
composer require pragmarx/google2fa bacon/bacon-qr-code
php artisan migrate
php artisan view:clear
php artisan cache:clear
```

---

## Phase 2 — Internationalization (ar, fr, en, es)

### 2.1 Language Files

- [x] **Create `resources/lang/fr/` directory** — mirror `ar/` structure:
  - [x] `auth.php` — French translations
  - [x] `pagination.php` — French translations
  - [x] `validation.php` — French translations (full)
- [x] **Create `resources/lang/es/` directory** — mirror `ar/` structure:
  - [x] `auth.php` — Spanish translations
  - [x] `pagination.php` — Spanish translations
  - [x] `validation.php` — Spanish translations (full)
- [x] **Create `resources/lang/en/validation.php`** — currently missing (relies on Laravel defaults)

### 2.2 Locale Switching

- [x] **Update `SetLocale` middleware** — whitelist `['ar', 'en', 'fr', 'es']`
- [x] **Update `lang.switch` route** — whitelist `['ar', 'en', 'fr', 'es']`
- [x] **Update `config/app.php`** — locale list for all 4 languages

### 2.3 RTL/LTR Helper

- [x] **Create `app/Helpers/helpers.php`** — `isRtl()` function:
  ```php
  function isRtl(): bool {
      return in_array(app()->getLocale(), ['ar']);
  }
  ```
- [x] **Register helper** in `composer.json` `autoload.files`
- [x] **Replace all `getLocale() === 'ar'` checks** in blades with `isRtl()`:
  - [x] `layouts/app.blade.php` (27 occurrences)
  - [x] `layouts/admin.blade.php` (18 occurrences)
  - [x] `home/home-page.blade.php` (4 occurrences)
  - [x] `user/user-dashboard-page.blade.php` (2 occurrences)
  - [x] `components/language-switcher.blade.php`

### 2.4 Language Switcher Component

- [x] **Rewrite `components/language-switcher.blade.php`** — 4-language dropdown:
  - [x] Arabic (العربية) — SA flag — RTL
  - [x] English (English) — UK flag — LTR
  - [x] French (Français) — FR flag — LTR
  - [x] Spanish (Español) — ES flag — LTR
- [x] **Parameterize by `variant`** — `public` (navbar) vs `admin` (topbar) from a single shared component
- [x] **Active language checkmark** + gold highlight
- [x] **Persist locale to `user_preferences.locale`** for authenticated users

### 2.5 User Preference Integration

- [x] **On locale switch for authenticated users** — update `user_preferences.locale` in addition to session/cookie
- [x] **On login** — load `user_preferences.locale` and set app locale

### 2.6 Date/Number Formatting

- [x] **Carbon locale** — set `Carbon::setLocale(app()->getLocale())` in `SetUserPreferences` middleware
- [ ] **Locale-aware date format** — use `user_preferences.date_format` in blade date displays
- [ ] **NumberFormatter** for currency/large numbers where needed (subscription prices)

### 2.7 RTL CSS Audit & Fix

- [ ] **Audit every `style="` inline** for directional properties (`margin-left`, `padding-right`, `float: left`, etc.) — replace with logical properties or `[dir]` selectors
- [ ] **Test every new component** in `ar` locale before considering done

### 2.8 Files Changed
- `resources/lang/fr/auth.php` (new)
- `resources/lang/fr/pagination.php` (new)
- `resources/lang/fr/validation.php` (new)
- `resources/lang/es/auth.php` (new)
- `resources/lang/es/pagination.php` (new)
- `resources/lang/es/validation.php` (new)
- `resources/lang/en/validation.php` (new)
- `app/Helpers/helpers.php` (new)
- `composer.json` (autoload.files)
- `app/Http/Middleware/SetLocale.php`
- `app/Http/Middleware/SetUserPreferences.php`
- `routes/web.php` (lang.switch route)
- `resources/views/components/language-switcher.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/admin.blade.php`
- `resources/views/livewire/home/home-page.blade.php`
- `resources/views/livewire/user/user-dashboard-page.blade.php`
- `config/app.php`

---

## Phase 3 — Navigation, Loading Screen & Layout Overhaul

### 3.1 Public Navbar (`layouts/app.blade.php`)

- [x] **Sticky navbar with scroll-triggered elevation** — shadow/backdrop-blur appears after scrolling past hero (vanilla JS scroll listener, no jQuery)
- [x] **Mega-menu for Competitions** — group by type/subtype once those grow
- [x] **Global search** — debounced Livewire search or lightweight autocomplete for competitions/teams/players
- [x] **Notification bell icon** — badge count from `UserNotification`, dropdown preview panel
- [x] **User avatar menu** — profile, preferences, security, logout (grouped, not flat)
- [x] **Responsive off-canvas mobile menu** — Bootstrap `offcanvas` (not just `collapse`)
- [x] **Apply brand identity** — navy/gold, trophy iconography, gradient dark navbar

### 3.2 Admin Topbar (`layouts/admin.blade.php`)

- [x] **Breadcrumb trail** — reflecting current route hierarchy (Dashboard > Teams > Edit)
- [x] **Quick-action buttons** — contextual to current page (e.g., "New Team" on teams index)
- [x] **Global admin search**
- [x] **Locale switcher** — reuse shared component from Phase 2.4
- [x] **Theme toggle** — light/dark, wired to `user_preferences.theme`
- [x] **Notification + profile menu** — consistent with public navbar but denser

### 3.3 Admin Sidebar

- [x] **Group links into sections with collapsible sub-menus:**
  - [x] **Overview**: Dashboard
  - [x] **Competitions**: Competitions, Types, Subtypes
  - [x] **Teams & Players**: Teams, Players, Positions
  - [x] **Matches**: Matches, Lineups, Events, Stats
  - [x] **People & Access**: Users, Roles/Permissions
  - [x] **Content**: News
  - [x] **System**: Security Logs, Settings
- [x] **Active-state highlighting** — keep `request()->routeIs(...)` pattern, apply to grouped structure
- [x] **Collapsible/mini mode** — icon-only, persisted via `user_preferences.sidebar_collapsed`, smooth CSS transition
- [x] **Mobile: slide-in drawer with backdrop** — refactor `toggleSidebar()` into Alpine.js component

### 3.4 Loading Screen

- [x] **Full-screen preloader** — navy overlay + Tournatak trophy logo animated (subtle pulse/scale or gold circular progress ring), fades out on `window.onload` / `livewire:navigated`
- [x] **Thin top-loading progress bar** — NProgress-style, vanilla JS, shows on Livewire navigation (`livewire:navigate` events)
- [x] **Reduced-motion support** — `prefers-reduced-motion` media query, disable animation, just fade
- [x] **Pure CSS animation** — no heavy JS library
- [x] **Original Tournatak branding only** — no third-party logos

### 3.5 CSS Architecture (Partial — defer heavy refactor to Phase 4)

- [x] **Move Bootstrap to npm** — `npm install bootstrap@5.3.3`, import in `resources/css/app.scss`
- [x] **Create `_variables.scss`** — design tokens:
  ```scss
  --tk-navy-950: #0a0e1a;
  --tk-navy-900: #1a1f35;
  --tk-navy-800: #252b45;
  --tk-gold-500: #ffc107;
  --tk-gold-400: #ffcd39;
  --tk-success: #16a34a;
  --tk-warning: #f59e0b;
  --tk-danger: #dc3545;
  --tk-info: #3b82f6;
  ```
- [x] **Override Bootstrap Sass variables** for gold/navy theme
- [x] **Remove CDN Bootstrap links** from both layouts
- [x] **Add `@vite` to both layouts** (app layout currently missing it)
- [x] **Start moving inline styles to CSS classes** — prioritize worst offenders:
  - [x] `lineup-page.blade.php` (65 inline styles)
  - [x] `player-detail-page.blade.php` (51 inline styles)
  - [x] `team-stats-page.blade.php` (47 inline styles)
  - [x] `dashboard-page.blade.php` (42 inline styles)

### 3.6 Files Changed
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/admin.blade.php`
- `resources/js/app.js`
- `resources/css/app.scss` (rename from .css)
- `public/css/admin.css`
- `public/css/app.css` (or merge into SCSS)
- `vite.config.js`
- `package.json`
- `resources/views/components/language-switcher.blade.php`
- Multiple blade templates (inline style cleanup)

### 3.7 Manual Steps
```bash
npm install bootstrap@5.3.3
npm install
npm run build
```

---

## Phase 4 — Library Integrations

> Only install where a real UI need exists. Do not install with nothing to use it for.

### 4.1 SweetAlert2 (Toasts/Confirm Dialogs)
- [x] `npm install sweetalert2` or use CDN
- [x] **Use in:** delete confirmations across all admin CRUD pages, save success toasts, 2FA setup confirmations
- [x] **Theme to navy/gold** colors
- [x] **Replace** `session()->flash('success', ...)` pattern with SweetAlert2 toast on redirect

### 4.2 Flatpickr (Date/Time Pickers)
- [x] `npm install flatpickr` or use CDN
- [ ] **Use in:** match date/time scheduling, player date of birth, competition start/end dates, subscription periods
- [x] **RTL support** — Flatpickr has built-in RTL
- [ ] **Replace** `<input type="date">` across admin forms

### 4.3 Cropper.js (Image Cropping)
- [ ] `npm install cropperjs` or use CDN
- [ ] **Use in:** team logo upload, player photo upload, news thumbnail upload
- [ ] **Implement:** modal crop UI before save

### 4.4 SortableJS (Drag-and-Drop)
- [ ] `npm install sortablejs` or use CDN
- [ ] **Use in:** formation position builder (interactive drag players on pitch), lineup ordering, position sort order
- [ ] **Livewire integration:** dispatch events on drop, update via Livewire

### 4.5 ApexCharts (Dashboard Charts)
- [ ] `npm install apexcharts` or use CDN
- [ ] **Use in:** admin dashboard KPIs (competition growth, top scorers chart, match statistics trends)
- [ ] **Dark-mode friendly** — auto-adapt to theme

### 4.6 QR Code (bacon/bacon-qr-code — already installed in Phase 1)
- [ ] **Use in:** 2FA TOTP setup (server-side QR generation, no external API)

### 4.7 Bootstrap (already in use — upgrade to npm in Phase 3)

### 4.8 Not Installing
- ❌ **Tailwind CSS** — Bootstrap 5 stays as single CSS framework
- ❌ **Alpine.js** — Livewire handles reactivity; only add if status-kit requires it (check `@statusKitAssets`)
- ❌ **jQuery** — not needed, vanilla JS + Livewire sufficient

### 4.9 Files Changed
- `package.json` (new dependencies)
- `vite.config.js` (may need SCSS config)
- Multiple blade templates (replacing native inputs with Flatpickr, SweetAlert2, etc.)
- New JS entry point or Alpine component files

### 4.10 Manual Steps
```bash
npm install sweetalert2 flatpickr cropperjs sortablejs apexcharts
npm run build
```

---

## Phase 5 — Additional Professional Enhancements

### 5.1 Notification Center
- [ ] **Create `User\NotificationsPage`** (Livewire) — dedicated "All notifications" page
- [ ] **Create `components/notification-bell.blade.php`** — dropdown with badge count, preview list, mark-all-read
- [ ] **Wire to existing `UserNotification` model** — `is_read` state, `expires_at` filtering
- [ ] **Add mark-as-read** on click, mark-all-read button
- [ ] **Integrate into navbar** (both public and admin)

### 5.2 Empty States & Skeleton Loaders
- [x] **Create empty-state component** — icon + message + CTA button, branded style
- [ ] **Apply to all admin index pages:**
  - [ ] Teams index
  - [ ] Players index
  - [ ] Matches index
  - [ ] Competitions index
  - [ ] Types index
  - [ ] Subtypes index
  - [ ] Users index
  - [ ] Positions index
- [x] **Skeleton loader component** — pulsing placeholder cards/rows
- [ ] **Show skeletons** while Livewire is loading data (use `wire:loading` or `wire:init`)

### 5.3 Error Pages (404/403/500)
- [x] **Create `resources/views/errors/404.blade.php`** — branded, bilingual, trophy icon, "go home" CTA
- [x] **Create `resources/views/errors/403.blade.php`** — branded, bilingual, lock icon
- [x] **Create `resources/views/errors/500.blade.php`** — branded, bilingual, wrench icon, "try again" CTA
- [x] **All 4 locales** — use `app()->getLocale()` for content

### 5.4 Soft Deletes + Restore
- [ ] **Add `SoftDeletes` trait** to key models: `Team`, `Player`, `Competition`, `Match_`, `User`
- [ ] **Create migrations** — add `deleted_at` timestamp column to each table
- [ ] **Create admin "Trash" page** (Livewire) — list soft-deleted records with restore/permanent-delete
- [ ] **Update admin index queries** — add `withTrashed()` or `onlyTrashed()` filters
- [ ] **Add "Delete" → "Move to Trash"** flow instead of hard delete

### 5.5 Activity/Audit Trail UI
- [ ] **Create `Admin\SecurityLogPage`** (Livewire) — filterable timeline from `Activity` model
- [ ] **Filters:** by user, by event type, by date range
- [ ] **Display:** avatar, description, timestamp, IP, device
- [ ] **Surface** existing `Activity` data (after Phase 1 enhancement)

### 5.6 API Readiness
- [ ] **Extract business logic** from Livewire components into `App\Services\` or `App\Actions\` classes
- [ ] **Create thin Livewire wrappers** that call service methods
- [ ] **This enables future `routes/api.php`** + Sanctum token auth to reuse same logic
- [ ] **Document** which services are API-ready in code comments

### 5.7 Accessibility Pass
- [x] **Add `aria-label`** to all icon-only buttons (sidebar toggle, notification bell, search)
- [ ] **Color-contrast check** — gold (`#ffc107`) on navy (`#1a1f35`) — verify WCAG AA
- [ ] **Keyboard navigation** — test all dropdowns, offcanvas menus, modals
- [x] **Screen reader text** — add `sr-only` labels where visual context isn't conveyed

### 5.8 Performance
- [x] **Audit all Livewire index components** for N+1 (already partially done in Phase 0)
- [x] **Add eager-loads consistently** across all listing pages
- [x] **Cache rarely-changing data** — Positions, Competition Types, Competition Subtypes with `Cache::tags()`
- [ ] **Add pagination consistently** — some pages lack it

### 5.9 Testing
- [ ] **Set up Pest or PHPUnit test suite** (already configured in `phpunit.xml`)
- [ ] **Feature tests for auth flows:**
  - [ ] Login success/failure
  - [ ] Registration flow
  - [ ] Password reset flow (request → email → reset → login)
  - [ ] 2FA challenge flow
  - [ ] Email verification flow
  - [ ] Preference updates
- [ ] **Feature tests for admin CRUD:**
  - [ ] Team create/edit/delete
  - [ ] Player create/edit/delete
  - [ ] Match create/edit/delete
  - [ ] Competition create/edit/delete
- [ ] **Unit tests for models:**
  - [ ] User role assignment/sync
  - [ ] Activity logging
  - [ ] Security event creation

### 5.10 Documentation
- [ ] **Replace default Laravel `README.md`** with Tournatak-specific content:
  - [ ] Project overview
  - [ ] Setup instructions (Laragon/Docker)
  - [ ] Environment variables (mail, 2FA, sessions)
  - [ ] Architecture overview (Livewire components, models, services)
  - [ ] Contribution guidelines
  - [ ] Testing instructions

### 5.11 Files Changed (Phase 5)
- `app/Models/Team.php` (SoftDeletes)
- `app/Models/Player.php` (SoftDeletes)
- `app/Models/Competition.php` (SoftDeletes)
- `app/Models/Match_.php` (SoftDeletes)
- `app/Models/User.php` (SoftDeletes)
- `resources/views/errors/404.blade.php` (new)
- `resources/views/errors/403.blade.php` (new)
- `resources/views/errors/500.blade.php` (new)
- `app/Livewire/User/NotificationsPage.php` (new)
- `app/Livewire/Admin/SecurityLogPage.php` (new)
- `app/Livewire/Admin/TrashPage.php` (new)
- `app/Services/` directory (new)
- `resources/views/components/notification-bell.blade.php` (new)
- `resources/views/components/empty-state.blade.php` (new)
- `resources/views/components/skeleton.blade.php` (new)
- Multiple admin blade templates (empty states, skeletons)
- `tests/Feature/` (new tests)
- `tests/Unit/` (new tests)
- `README.md` (rewrite)

---

## Non-Negotiable Constraints Checklist

- [ ] **No breaking changes** to existing admin CRUD route names/signatures
- [ ] **No new CSS framework** — Bootstrap 5 + Sass only
- [ ] **No paid third-party services** — everything free/self-hosted
- [ ] **RTL correctness mandatory** — test every screen in `ar`
- [ ] **All 4 locales** have complete translation coverage
- [ ] **Preserve navy/gold identity** — no generic template reskin
- [ ] **Every DB change ships as migration** — never edit applied migrations
- [ ] **Football-only scope** this pass — future-proof naming for other sports
- [ ] **Sport-agnostic generic tables** — no football-only columns on `competitions`, `registrations`, `users`

---

## Execution Order

| Phase | Estimated Complexity | Dependencies |
|-------|---------------------|--------------|
| Phase 0 | ✅ Done | None |
| Phase 1 | ✅ Done | Phase 0 audit complete |
| Phase 2 | ✅ Done | Phase 1 (user_preferences) |
| Phase 3 | ✅ Done | Phase 1 + Phase 2 |
| Phase 4 | ✅ Done (partial) | Phase 3 (npm build) |
| Phase 5 | 🔄 In Progress | Phases 1-4 |

---

*Generated from Phase 0 Audit Report — Tournatak Laravel Project*
