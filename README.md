# Tournatak - Sports Tournament Management Platform

A professional, multi-language sports tournament management platform built with Laravel 12 + Livewire 4, designed for football and futsal competitions.

## Features

### Core Functionality
- **Team Management** — Create, edit, and manage teams with captain assignment, logos, and staff
- **Player Management** — Full player profiles with stats, positions, medical records, and career history
- **Match Management** — Schedule matches, track lineups, events (goals/cards/subs), and statistics
- **Competition Management** — Organize tournaments with types, subtypes, registration, and approval workflows
- **Position System** — Configurable positions for both football and futsal

### Admin Dashboard
- Real-time KPIs and statistics
- Activity/audit trail (Security Log)
- User management with role-based access (Spatie Permission)
- Soft-deleted records with Trash and Restore
- Collapsible sidebar with grouped navigation

### Authentication & Security
- **Two-Factor Authentication** — TOTP authenticator app support with QR code setup
- **Recovery Codes** — 8 single-use backup codes for 2FA
- **Email Verification** — Custom verification flow with branded emails
- **Password Reset** — Full forgot/reset password flow with rate limiting
- **Security Activity Log** — Track logins, 2FA events, password changes, and more
- **Rate Limiting** — Login (5/min) and password reset (3/min) throttling
- **Session Management** — Regeneration on login, invalidation on logout

### Multi-Language Support (4 Locales)
- **Arabic** (العربية) — RTL layout, Arabic validation messages
- **English** — LTR layout
- **French** (Français) — LTR layout
- **Spanish** (Español) — LTR layout
- Language switcher with user preference persistence

### UI/UX
- **Dark Theme** — Navy/gold brand identity (`#0a0e1a` + `#ffc107`)
- **Bootstrap 5.3** — Built via npm/SCSS, no CDN dependency
- **Flatpickr** — Locale-aware date/time pickers with 4-language support
- **SweetAlert2** — Themed toast notifications and confirmation dialogs
- **Skeleton Loaders** — Pulsing placeholders during Livewire loading
- **Empty States** — Branded empty state components across all admin pages
- **Error Pages** — Custom 404, 403, 500 pages with 4-locale support
- **Preloader** — Full-screen loading animation with progress bar
- **Responsive** — Mobile offcanvas navigation, collapsible sidebar

### Notifications
- Real-time notification bell with unread badge
- Full notifications page with filter (All/Unread/Read)
- Mark as read / Mark all read / Delete

### Internationalization
- RTL/LTR automatic switching via `isRtl()` helper
- Locale-aware date formatting
- Full validation message translation (ar/en/fr/es)

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12.64, PHP 8.2 |
| Frontend | Livewire 4.3, Bootstrap 5.3, SCSS |
| Database | MySQL 8.4 (SQLite for testing) |
| Auth | Spatie Permission 6.25 (roles/permissions) |
| 2FA | pragmarx/google2fa + bacon/bacon-qr-code |
| Build | Vite 7, npm, Sass |
| Testing | Pest 3.8 (30 tests, 41 assertions) |
| Date Pickers | Flatpickr 4.6 |
| Alerts | SweetAlert2 11 |
| Icons | Bootstrap Icons, Font Awesome |

## Requirements

- PHP 8.2+
- Composer 2.x
- Node.js 20+
- MySQL 8.x or SQLite (for testing)

## Installation

### Using Laragon (Windows)

```bash
# Clone the repository
git clone <repository-url> C:\laragon\www\tournatak-laravel

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=tournatak_laravel
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations and seed
php artisan migrate
php artisan db:seed

# Install Node dependencies and build
npm install
npm run build

# Clear caches
php artisan view:clear
php artisan cache:clear
```

### Using Docker

```bash
# Using Laravel Sail
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### Quick Setup

```bash
composer setup
```

This runs: `composer install` → `.env` setup → `key:generate` → `migrate` → `npm install` → `npm run build`

## Environment Variables

Key variables to configure in `.env`:

```env
APP_NAME="Tournatak"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://tournatak-laravel.test

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tournatak_laravel
DB_USERNAME=root
DB_PASSWORD=

# Mail (for verification & password reset emails)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tournatak.com
MAIL_FROM_NAME="${APP_NAME}"

# Security
SESSION_SECURE_COOKIE=true
```

## Development

```bash
# Run all services (server + queue + vite)
composer dev

# Run tests
composer test
# or
php artisan test

# Format code
./vendor/bin/pint

# View logs
php artisan pail
```

## Architecture

### Livewire Components

```
app/Livewire/
├── Admin/                    # Admin panel (role:admin middleware)
│   ├── DashboardPage.php     # KPIs, recent activity, charts
│   ├── TrashPage.php         # Soft-deleted records management
│   ├── SecurityLogPage.php   # Audit trail with filters
│   ├── Competitions/         # CRUD + type/subtype management
│   ├── Teams/                # CRUD + staff, formations, tactics, medical, stats
│   ├── Players/              # CRUD with profile details
│   ├── Matches/              # CRUD + lineups, events, stats
│   ├── Types/                # Competition type CRUD
│   ├── Subtypes/             # Competition subtype CRUD
│   ├── Users/                # User management with role assignment
│   └── Positions/            # Position management with pagination
├── Auth/                     # Authentication flows
│   ├── LoginPage.php         # Login with 2FA redirect
│   ├── RegisterPage.php      # Registration with role assignment
│   ├── ForgotPasswordPage.php
│   ├── ResetPasswordPage.php
│   └── TwoFactorChallengePage.php
├── User/                     # User panel
│   ├── UserDashboardPage.php
│   ├── ProfilePage.php
│   ├── SecurityPage.php
│   ├── NotificationsPage.php
│   └── NotificationBell.php  # Dropdown bell component
├── Public/                   # Public pages
│   ├── CompetitionsPage.php
│   ├── TeamsPage.php
│   ├── PlayersPage.php
│   ├── TeamDetailPage.php
│   └── PlayerDetailPage.php
├── Home/HomePage.php         # Landing page
└── Security/TwoFactorSetupPage.php
```

### Models

```
app/Models/
├── User.php                  # HasRoles, SoftDeletes, observer-synced role
├── Team.php                  # SoftDeletes, captain, players, competitions
├── Player.php                # SoftDeletes, user, team, goals, stats
├── Match_.php                # SoftDeletes, teams, events, lineups
├── Competition.php           # SoftDeletes, type, subtype, organizer
├── Activity.php              # Security events, user activities
├── UserNotification.php      # User notifications
├── UserPreference.php        # Locale, theme, sidebar settings
├── SecuritySetting.php       # 2FA config per user
├── Position.php              # Cached active positions
├── CompetitionType.php       # Tournament types
├── CompetitionSubtype.php    # Tournament subtypes
├── TeamStaff.php             # Coaching/medical staff
├── Formation.php             # Tactical formations
├── TeamTactic.php            # Team tactical settings
├── TeamMedicalRecord.php     # Injury/medical tracking
├── MatchLineup.php           # Match lineups with positions
├── MatchEvent.php            # Goals, cards, substitutions
├── MatchStat.php             # Match statistics
├── Goal.php                  # Goal records
└── ... (more models)
```

### Services

```
app/Services/
├── SecurityActivityLogger.php  # Centralized security event logging
```

### Middleware

| Middleware | Purpose |
|-----------|---------|
| `SetLocale` | Locale detection from URL/session/cookie |
| `SetUserPreferences` | Apply user locale, theme, sidebar preferences |

### Key Helpers

```php
isRtl()     // Returns true if current locale is RTL (Arabic)
```

## Testing

Tests use Pest with SQLite in-memory database. The test suite covers:

- **Auth flows** — Login (success/failure), Registration (valid/duplicate), Password Reset
- **Admin access** — CRUD pages require admin role, guests/non-admins denied
- **Model operations** — Soft delete, restore, trash page access
- **Public pages** — Home, teams, players, locale switching, 404

```bash
php artisan test          # Run all tests
php artisan test --filter=LoginTest  # Run specific test file
```

## Database Schema

Key tables: `users`, `teams`, `players`, `competitions`, `matches`, `match_lineups`, `match_events`, `goals`, `positions`, `notifications`, `activities`, `roles`, `permissions`

Soft deletes enabled on: `teams`, `players`, `competitions`, `matches`, `users`

## License

MIT License. See [LICENSE](LICENSE) for details.
