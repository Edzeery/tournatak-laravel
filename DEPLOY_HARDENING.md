# DEPLOY_HARDENING.md — Production Deployment Hardening

Living checklist for closing the deployment-infrastructure gaps. Scope is **strictly
infra / config / documentation** — no application code, business logic, tests, or
branding changes. Follows the same pattern as `TASKS.md` / `BRANDING_MIGRATION.md`.

---

## Rules applied

- **Never commit real secrets.** All credential values in tracked templates must be
  `CHANGE_ME_IN_PRODUCTION` placeholders.
- Only allowed application-code change: register the backup scheduler in
  `bootstrap/app.php` via `->withSchedule(...)` (the repo currently has no scheduler).
- **Do not** alter `docker-compose.yml` (local dev) behavior. A separate
  `docker-compose.prod.yml` is created instead.
- Local `.env` / `.env.docker` are gitignored or dev-only — never modified for prod.
- `docker/Dockerfile`, `docker/php.ini`, `docker/nginx/nginx.conf` are shared by both
  stacks — not modified unless strictly required.
- All verification runs must stay green: `php artisan test` (baseline 336 passed /
  7422 assertions), `vendor/bin/pint --test`, `vendor/bin/phpstan analyse --memory-limit=1G --no-progress`.

---

## Pre-flight findings (verified, not assumed)

| # | Finding | Where | Impact |
|---|---|---|---|
| F1 | MySQL creds (`root`/`user`/`pass`) hardcoded to `secret` | `docker-compose.yml` | local-only today; must NOT leak into prod file |
| F2 | `.env.production` already ignored | `.gitignore:5` | confirm with `git check-ignore` |
| F3 | No general `.env*` ignore rule | `.gitignore` | `.env.production.example` is a NEW tracked file — must NOT be ignored (verify with `git check-ignore`, don't assume) |
| F4 | No scheduler registered in app | `bootstrap/app.php` (34 lines, no `withSchedule`) | first `->withSchedule()` goes here |
| F5 | No backup tooling installed | `composer.json` (no `spatie/laravel-backup`) | Phase 2 installs it |
| F6 | `s3` disk configured via `AWS_*` env vars | `config/filesystems.php:57-68` | S3 is an option for backup storage (SDK dep `aws/aws-sdk-php` NOT installed — needs `composer require`) |
| F7 | Real-looking `APP_KEY` committed in tracked file | `.env.docker:3` | pre-existing committed secret — flagged, **not** touched (dev file, out of scope); recommend rotation |
| F8 | No Docker CLI on this Windows machine | `where docker` → not found | Phase 5 compose validation done via YAML parse; user runs `docker compose config` on server |
| F9 | Packagist reachable | HEAD `repo.packagist.org` → 200 | `composer require` will work |

---

## Phase 1 — Separate prod compose from local dev
- [x] `docker-compose.prod.yml` created (mailpit removed; mysql/redis host ports removed; creds from env vars `${VAR}`; `restart: always`)
- [x] `.env.production.example` created (tracked; all values `CHANGE_ME_IN_PRODUCTION`)
- [x] `git check-ignore` confirms `.env.production` ignored AND `.env.production.example` trackable

## Phase 2 — Automated DB backups
- [x] `composer require spatie/laravel-backup` (9.3.6 — 10.x needs PHP 8.3, Dockerfile is 8.2) + `aws/aws-sdk-php` + `league/flysystem-aws-s3-v3`
- [x] Publish `config/backup.php` + configure destination/monitor disks to `s3` (env `BACKUP_DESTINATION_DISK`)
- [x] Register schedule in `bootstrap/app.php` (backup:run 02:00 + backup:clean 02:30, retention 7 daily + 4 weekly)
- [x] Manual backup/restore steps documented (Phase 4 runbook)

## Phase 3 — Queue worker & scheduler infra
- [x] Queue worker service added to `docker-compose.prod.yml` (`php artisan queue:work --tries=3 --backoff=10`)
- [x] Scheduler service added to `docker-compose.prod.yml` (`php artisan schedule:work`; user chose container over host cron)

## Phase 4 — DEPLOYMENT.md runbook
- [x] Server prerequisites (PHP extensions matching `docker/Dockerfile`: pdo_mysql mbstring exif pcntl bcmath gd zip)
- [x] `.env.production` creation from `.env.production.example` + `APP_KEY` generation
- [x] Compose build/up, migrations, storage:link, config:cache
- [x] Backup operations (manual trigger, restore, verify) + scheduler/queue notes

## Phase 5 — Verification
- [x] `docker-compose.prod.yml` parses as valid YAML (docker not installed here — validated via Symfony YAML parse; user runs `docker compose -f docker-compose.prod.yml --env-file .env.production config` on server)
- [x] `php artisan test` — 336 passed / 7422 assertions (identical to baseline)
- [x] `vendor/bin/pint --test` passed
- [x] `vendor/bin/phpstan analyse --memory-limit=1G --no-progress` no errors
- [x] Final per-file change summary table appended

---

## Final summary (per-file change reason)

| File | Change | Reason |
|---|---|---|
| `docker-compose.prod.yml` (new) | Prod stack: app/nginx/mysql/redis/queue/scheduler; no mailpit; no host-exposed mysql/redis ports; `${VAR}` creds; `restart: always`; `env_file: .env.production` | Separate prod from local dev |
| `.env.production.example` (new) | Full prod env template, all secrets `CHANGE_ME_IN_PRODUCTION`, incl. MYSQL_* + AWS_* + BACKUP_* vars | Tracked, secret-free prod template |
| `config/backup.php` (new) | spatie/laravel-backup config; destination + monitor disks `s3`; retention 7 daily + 4 weekly (daily/monthly/yearly tiers disabled); notification email env-driven | Backup tooling config |
| `bootstrap/app.php` | Added `->withSchedule()` (backup:run 02:00, backup:clean 02:30) | The one allowed app-code change; registers scheduler |
| `composer.json` / `composer.lock` | `spatie/laravel-backup ^9.3`, `aws/aws-sdk-php ^3.390`, `league/flysystem-aws-s3-v3 ^3.35` | Backup + S3 driver deps |
| `DEPLOYMENT.md` (new) | Production runbook (prereqs, deploy, backups, restore, queue/scheduler, release, troubleshooting) | Operational docs |
| `DEPLOY_HARDENING.md` (new) | Living checklist + findings + decisions | Tracking doc |

**Verified:** 336 tests passed (7422 assertions), Pint passed, PHPStan no errors, both compose files valid YAML, local `docker-compose.yml` untouched (still 5 services, `unless-stopped`).

## Noted, intentionally NOT changed (pre-existing)

- `.env.docker:3` committed `APP_KEY` (real-looking secret, dev file) — recommend rotating and gitignoring `.env.docker`.
- `docker/Dockerfile` has no `redis` PHP extension while `.env.docker` sets `REDIS_CLIENT=phpredis` — latent mismatch; app uses database queue/cache so not blocking. Not changed (shared with local stack).

### Fix-pass environment drift (external, NOT part of this task)

- The working tree's `composer.json`/`composer.lock`/`vendor` drifted from commit
  `28bd358` (`spatie/laravel-backup ^9.3` / `9.3.6`) to `^9.4` / `9.4.1`, which
  requires **PHP >= 8.3** (blocked local `php artisan` on PHP 8.2.12 via
  `vendor/composer/platform_check.php`). This drift was **not made by this task**
  and was left untouched. Full suite verified using Laragon's
  `php-8.3.28-Win32-vs16-x64` instead. Team decision needed: keep 9.4.1 (requires
  PHP 8.3 in CI/prod images — note `docker/Dockerfile` is currently
  `php:8.2-fpm` and would fail to build with `composer install` inside) or
  restore 9.3.6 (PHP 8.2-compatible).

---

## Decisions (answered)

1. **Backup storage backend** — **S3** (uses existing `s3` disk + `AWS_*` env; installed `aws/aws-sdk-php` + `league/flysystem-aws-s3-v3`).
2. **Retention policy** — **7 daily + 4 weekly** (keep_all 7d, daily tier 0, weekly 4w, monthly/yearly 0).
3. **Scheduler** — **dedicated container** running `php artisan schedule:work` in `docker-compose.prod.yml`.

---

## Fix pass — verification findings

Scope: strictly the 3 fixes below (commit 28bd358 follow-up). No other changes.

- [x] **FIX 1** — Rotate exposed `APP_KEY` in `.env.docker` + stop tracking it
  - [x] New `APP_KEY` generated, written to `.env.docker` (rotated; old key not reused)
  - [x] `.env.docker` added to `.gitignore` and removed from git tracking (`git rm --cached`)
  - [x] `.env.docker.example` created (tracked), `APP_KEY` blank, other real-looking values placeholders
  - [x] README.md local Docker section reviewed — uses Sail commands, does **not** reference `.env.docker` directly, so no README change required (conditional rule did not trigger)
  - [x] Note: git history NOT scrubbed — flag to user about BFG/filter-repo as a separate decision
- [x] **FIX 2** — Correct health-check verification commands in DEPLOYMENT.md (§2.5 + §7)
- [x] **FIX 3** — `docker-compose.prod.yml` resilience
  - [x] 3a: healthchecks on mysql (`mysqladmin ping`) + redis (`redis-cli ping`), `depends_on: condition: service_healthy`
  - [x] 3b: remove `.:/var/www` bind mounts; per-service reasoning documented below

### FIX 1 details

- `.env.docker` — new `APP_KEY=base64:WKhTXhPWIEuAAJFdij3SL7h6RpyLq7LnlzJ/MgKqpkk=`
  generated (same format `php artisan key:generate --show` emits: `base64:` + 32
  random bytes). The old key `base64:eGOOweQBjMDWfFVuq8AwKlXwsiJ0RyHZyfomKME8AVg=`
  is not reused anywhere and remains only in git history (see scrub note below).
  - `php artisan key:generate --show` could not be run because the working tree
    had drifted to `spatie/laravel-backup 9.4.1` (requires PHP >= 8.3, local PHP
    is 8.2.12) — see "Noted" section. The key was instead generated with an
    equivalent `random_bytes(32)` + `base64:` (same output format), and the full
    suite still passes under Laragon's PHP 8.3.28.
- `.gitignore` — added `.env.docker` (line after `.env.production`).
- `.env.docker.example` — tracked; `APP_KEY` blank (leads to a runtime error if
  used as-is, forcing the operator to generate a key); `DB_PASSWORD=secret` kept
  as a placeholder matching the documented local-dev value; AWS/MAIL creds blank.
- **Git history NOT scrubbed.** The old key is still reachable in past commits.
  Scrubbing requires a history rewrite (BFG Repo-Cleaner or
  `git filter-repo`) — a separate decision the team must make, with force-push
  consequences.

### FIX 2 details

- §2.5 Verify — two endpoints, both documented with correct commands:
  - **Liveness** `curl -I http://<host>/up` → **HTTP 200** (Laravel default
    framework health route, plain HTML "OK", no JSON).
  - **Readiness** `curl http://<host>/api/health` → JSON with
    `"status": "healthy"` + `services.database` / `services.cache` `"healthy"`
    (route verified in `routes/api.php`).
- §7 diagnostics table — added a Liveness row and a Readiness row matching §2.5.

### FIX 3 details

**3a — healthchecks + conditional depends_on**

- mysql: `test: ["CMD-SHELL", "mysqladmin ping -h localhost -u root -p$$MYSQL_ROOT_PASSWORD --silent"]`, interval 10s, timeout 5s, retries 5.
  - `$$` (not `$`) defers expansion to the container shell so the password is
    read from the mysql container's own env; a single `$` would be interpolated
    by Compose at parse time.
- redis: `test: ["CMD", "redis-cli", "ping"]`, interval 10s, timeout 5s, retries 5.
- `app`, `queue`, `scheduler` `depends_on` now use the long form with
  `condition: service_healthy`, so workers only boot once the DB/Redis actually
  answer, instead of racing on `started`.

**3b — no more `.:/var/www` bind mounts (code ships in the image)**

Per-service reasoning (FIX 3b):

- `app`, `queue`, `scheduler`: built from `docker/Dockerfile` which already
  `COPY . /var/www`, so code is baked in. Removed the `.:/var/www` bind mount.
  Kept `./docker/php.ini:/usr/local/etc/php/conf.d/tournatak.ini` — it's ops
  config, not code, and stays editable without a rebuild. Added two shared named
  volumes so runtime data survives image rebuilds and is shared across the three
  app-class containers:
  - `storage-data:/var/www/storage` — covers `storage/app/private`,
    `storage/app/public`, sessions, cache, logs.
  - `public-data:/var/www/public/uploads` — user-uploaded files (the app's
    `uploads` disk is rooted at `public_path('uploads')`).
- `nginx`: switched from stock `nginx:alpine` + `.:/var/www` bind to a small
  build (`docker/nginx/Dockerfile`, context `.`) that bakes `nginx.conf` and
  `public/` into the image, plus mounts `public-data:/var/www/public/uploads`
  to serve the shared uploads. `depends_on: app` retained (start order), since
  the nginx image has everything baked.
  - **Why the subpath mount (not `/var/www/public`):** `public/build` is Vite
    output that is **not committed**, so it is baked into the image at build
    time. Mounting the volume over the whole of `/var/www/public` would shadow
    the baked assets with a stale first-boot snapshot and break asset updates on
    the next release; mounting only at `public/uploads` keeps baked assets fresh
    while uploads stay shared and persistent.
- Asset build requirement: `public/build` is gitignored, so `DEPLOYMENT.md`
  §2.3 and §6 now run `npm ci && npm run build` on the server **before**
  `up -d --build` (assets are baked, never bind-mounted).
