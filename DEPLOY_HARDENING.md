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

---

## Decisions (answered)

1. **Backup storage backend** — **S3** (uses existing `s3` disk + `AWS_*` env; installed `aws/aws-sdk-php` + `league/flysystem-aws-s3-v3`).
2. **Retention policy** — **7 daily + 4 weekly** (keep_all 7d, daily tier 0, weekly 4w, monthly/yearly 0).
3. **Scheduler** — **dedicated container** running `php artisan schedule:work` in `docker-compose.prod.yml`.
