# Bracketa — Multi-Domain Competition Platform

A professional, multi-language competition management platform built with **Laravel 12 + Livewire 4**, powering **five competition domains** from one shared `Competition` core: **sports**, **esports**, **academic** (quiz), **hackathon**, and **creative arts**.

Each domain chooses how competitions are evaluated:

- **Match-based** (sports, esports) — teams, fixtures, live match control, lineups, events, and standings.
- **Submission-based** (academic, hackathon, creative) — rounds, participant submissions, assigned judges, and aggregated scoring/ranking.

## Features

### Core Functionality
- **Multi-Domain Model** — `CompetitionDomain` top-level grouping; each competition resolves an evaluation basis (`match` | `submission`) and participant basis (`team` | `individual` | `both`)
- **Competition Management** — Domain-first creation wizard (steps adapt per domain), types/subtypes, registration, and approval workflows
- **Submission Competitions** — rounds, submissions (team/individual), judge assignment, per-criteria scoring, and live rankings
- **Team & Player Management** — teams with captains/logos/staff, player profiles, positions, medical records, stats
- **Match Management** — fixtures, live match control (phases + timers), lineups on an interactive pitch, events (goals/cards/subs), statistics
- **Position System** — configurable positions per sport

### Admin Panel
- Real-time KPIs and charts (lazy-loaded ApexCharts)
- Activity/audit trail (Security Log)
- User management with role-based access (Spatie Permission)
- Domains overview page, sports/types/subtypes/positions/referees/registrations management
- Soft-deleted records with Trash and Restore

### Judge Experience
- Dedicated judge panel at `/judge/competitions/{competition}` (auth-only; authorization via policies)
- Judges score only their assigned submissions for the active round
- Other judges' scores hidden by default (organizer can opt out per competition)
- Results aggregate automatically through the scoring engine registry

### Authentication & Security
- **Two-Factor Authentication** — TOTP authenticator app with QR setup + 8 recovery codes
- **Email Verification** — custom branded flow
- **Password Reset** — throttled (3/min) forgot/reset flow
- **Security Activity Log** — logins, 2FA events, password changes, and more
- **Rate Limiting** — login (5/min), password reset (3/min)
- **Session Management** — regeneration on login, invalidation on logout

### Multi-Language (4 Locales, all in sync)
- **Arabic** (العربية) — RTL layout · **English** · **French** · **Spanish**
- Language switcher with per-user preference persistence; locale-aware dates and validation messages

### UI / Design System
- **Two-layer brand identity**: sports UI keeps its navy/gold (`#0a0e1a` + `#ffc107`) look, while domain-neutral surfaces (homepage, nav highlights, wizard, domain badges) use the platform brand — deep indigo `#1E1B4B` + amber `#F5A622` (`--brand-*` tokens in `_variables.scss`)
- Bootstrap 5.3 built via npm/SCSS (no CDN), dark/light themes, RTL-ready
- Lazy-loaded ApexCharts, page-scoped Vite entries (lineup board), Flatpickr, SweetAlert2, skeleton loaders, branded empty states, 4-locale error pages, preloader

### Notifications
- Real-time notification bell with unread badge
- Full notifications page with filters and read/delete actions

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2 |
| Frontend | Livewire 4, Alpine.js, Bootstrap 5.3, SCSS |
| Database | MySQL 8.4 (SQLite in-memory for tests) |
| Auth / ACL | Spatie Permission (roles/permissions) |
| 2FA | pragmarx/google2fa + bacon/bacon-qr-code |
| Charts | ApexCharts (dynamic `import()` — dashboard only) |
| Build | Vite 7 + Sass (`app.js` 266 kB gzipped ~80 kB) |
| Testing | Pest 3.8 (**336 tests, 7422 assertions**) |
| Static analysis | PHPStan (Larastan), Pint |
| Observability | Sentry |

## Requirements

- PHP 8.2+
- Composer 2.x
- Node.js 20+
- MySQL 8.x or SQLite (for testing)

## Installation

### Using Laragon (Windows)

```bash
git clone <repository-url> C:\laragon\www\tournatak-laravel
cd tournatak-laravel

composer install
cp .env.example .env
php artisan key:generate

# Configure database in .env, then:
php artisan migrate
php artisan db:seed

npm install
npm run build

php artisan view:clear
php artisan cache:clear
```

### Quick Setup

```bash
composer setup
```

Runs: `composer install` → `.env` → `key:generate` → `migrate` → `npm install` → `npm run build`.

### Using Docker (Laravel Sail)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

## Environment Variables

```env
APP_NAME="Bracketa"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://tournatak-laravel.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tournatak_laravel
DB_USERNAME=root
DB_PASSWORD=

# Mail (verification & password reset)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bracketa.com
MAIL_FROM_NAME="${APP_NAME}"

# Security
SESSION_SECURE_COOKIE=true
```

## Development

```bash
composer dev                  # server + queue + vite
composer test                 # or php artisan test
vendor/bin/pint --test        # style check
vendor/bin/phpstan analyse --memory-limit=1G   # static analysis
php artisan pail              # logs
```

## Domain Model & Extension Points

```
CompetitionDomain (1) ──< (n) Competition (1) ──< (n) Registration
      │                                  ├──< (n) Match_        (match-based evaluation)
      │                                  ├──< (n) CompetitionRound (1) ──< (n) Submission (1) ──< (n) JudgeScore
      │                                  └──< (n) Judge
      │
      └── Sport (implicit: sports always map to the "sports" domain)
```

- `competition_domains.slug` is the stable identity (`sports`, `esports`, `academic`, `hackathon`, `creative`).
- `competition_domains.evaluation_basis` / `participant_basis` are string columns cast to PHP enums (no DB ENUMs — they break SQLite/DBAL).
- `competitions.domain_id` is nullable (`nullOnDelete`), backfilled to `sports`.
- `CompetitionDomain` helpers: `usesMatchEvaluation()`, `usesSubmissionEvaluation()`, `supportsTeams()`, `supportsIndividuals()`, `localizedName()`.
- **Scoring is pluggable**: `app/Contracts/ScoringEngineInterface` → `ScoringEngineRegistry` resolves an engine from `$competition->evaluationBasis()`. Two engines ship: `SportsScoringEngine` (`match`) and `SubmissionScoringEngine` (`submission`).

### Adding a 6th domain (runbook)

1. Add the row to `database/migrations/2026_08_01_000001_create_competition_domains_table.php` **and** `CompetitionDomainSeeder` (same data) — include `evaluation_basis`, `participant_basis`, and all localized `name_*`/`description_*` columns.
2. Add the slug constant to `CompetitionDomain::SLUGS`.
3. Match-based domains reuse `TournamentFormatService` + `SportsScoringEngine` automatically. Submission domains reuse the rounds/submissions/judging flow. Implement a new `ScoringEngineInterface` **only** if scoring is genuinely new, and register it in `AppServiceProvider`.
4. Surface it in UI if desired: homepage tile + `domain_<slug>` nav items (per-locale `app.*` keys).
5. Run `migrate:fresh --seed`, the full test suite, Pint, and PHPStan. No changes to `Match_`/`MatchEvent` or sports-domain code are needed.

### Adding a new sport

1. Seed the sport (inline in the `create_sports_table` migration, mirroring football/futsal) or via seeder.
2. Define `positions` rows for the sport (goalkeeper/defender/midfielder/forward categories, with abbreviations).
3. Competitions for the sport are created through the sports domain's normal wizard (`sport_id` + format).
4. Optionally add a formation template + `formations` row; the lineup page renders any sport's positions on the pitch.

## Architecture

### Livewire components

```
app/Livewire/
├── Admin/                        # /panel (admin role)
│   ├── DashboardPage.php         # KPIs + lazy-loaded ApexCharts
│   ├── TrashPage.php             # soft-deleted records
│   ├── SecurityLogPage.php       # audit trail
│   ├── CompetitionDomainsPage.php# read-only domains overview
│   ├── Competitions/             # CRUD + wizard, rounds, submissions, judging
│   ├── Matches/                  # CRUD + live control, lineup, events, stats
│   ├── Teams/ | Players/ | Positions/ | Referees/ | Registrations/
│   ├── Sports/ | Types/ | Subtypes/ | Users/
├── Judge/
│   └── JudgingPage.php           # /judge/competitions/{competition}
├── Public/                       # competitions (with ?domain= filter), teams, matches, players, live match
├── Home/HomePage.php             # landing page (domain showcase + how-it-works)
├── User/                         # dashboard, profile, security, notifications, preferences, registrations
├── Auth/ | Security/             # auth flows + 2FA setup
```

### Services & contracts

```
app/Services/
├── ScoringEngine.php               # low-level sports points/tiebreaker utility
├── SportsScoringEngine.php         # ScoringEngineInterface (match)
├── SubmissionScoringEngine.php     # ScoringEngineInterface (submission)
├── ScoringEngineRegistry.php       # resolves engine by evaluation basis
├── CompetitionSetupService.php     # wizard steps/fields/validation + per-domain type provisioning
├── CompetitionService.php          # domain-aware create()
├── TournamentFormatService.php     # match pairing + round generation
├── RegistrationService.php         # domain-guarded registration
├── StandingService / MatchService / TeamService / PlayerService
├── UserService / AuthService / NotificationService / SecurityActivityLogger
app/Contracts/ScoringEngineInterface.php
app/Enums/CompetitionEvaluationBasis.php, CompetitionStatus.php, MatchStatus.php,
       ParticipantType.php, RegistrationStatus.php, SubmissionStatus.php, ApprovalStatus.php
app/Policies/CompetitionPolicy.php, JudgePolicy.php, SubmissionPolicy.php, JudgeScorePolicy.php
```

### Frontend build

- `resources/js/app.js` — eager core (Bootstrap, Flatpickr + ar/fr/es locales, SweetAlert2, Alpine timer). **ApexCharts is lazy** via `window.loadApexCharts()`.
- `resources/js/lineup.js` — page-scoped Vite entry for the interactive lineup board (loaded only on that page).
- `resources/css/app.scss` — core tokens, components, layouts, pages, panels, vendor overrides.

## Testing

Pest + SQLite in-memory. The suite covers auth/2FA flows, role-gated admin access, model operations, public pages, the multi-domain model, the domain-first wizard, registration guards, submission/judging end-to-end, and scoring engines.

```bash
php artisan test                          # full suite (336 tests, 7422 assertions)
php artisan test --filter=SubmissionCompetitionFlowTest
```

## Continuous Integration

GitHub Actions (`.github/workflows/ci.yml`) gates on: `composer audit`, `npm audit`, `npm run build`, Pint, **PHPStan**, and the full test suite against both SQLite and MySQL 8.4.

## License

MIT License. See [LICENSE](LICENSE) for details.
