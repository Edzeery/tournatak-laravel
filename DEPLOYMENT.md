# DEPLOYMENT.md — Production Deployment Runbook

Operational guide for deploying **Bracketa** to production using
`docker-compose.prod.yml`. Complements `DEPLOY_HARDENING.md` (the hardening checklist)
and the local setup docs in `README.md`.

**Local vs production stacks are fully separate:**
`docker-compose.yml` (local dev: mailpit, exposed 3307/6380 ports, hardcoded `secret`
creds) is **never** used in production. Use `docker-compose.prod.yml` + `.env.production`.

---

## 1. Server prerequisites

- Linux host with Docker Engine + Docker Compose v2.
- Git (to clone the repo).
- The app runs in containers, so no PHP/Node/Composer are required on the host —
  except for the one-time secret generation step below (can also be run in a container).

### Required PHP extensions (already in the app image)

Provided by `docker/Dockerfile` (base `php:8.2-fpm`):

`pdo_mysql`, `mbstring`, `exif`, `pcntl`, `bcmath`, `gd`, `zip`, plus `curl`, `git`,
Composer. The `pcntl` extension is required for `schedule:work` and `queue:work`
(signal handling) — included.

> Note: `REDIS_CLIENT=phpredis` is set in env, but the `redis` PHP extension is not
> installed in the image. The app is configured with database-driven cache/queue
> (`CACHE_STORE=database`, `QUEUE_CONNECTION=database`), so Redis is not exercised in
> the current runtime. The Redis container is provisioned for future use.

---

## 2. First-time deployment

### 2.1 Clone the repo

```bash
git clone <repository-url> /var/www/bracketa
cd /var/www/bracketa
```

### 2.2 Create the production environment file

```bash
cp .env.production.example .env.production
nano .env.production
```

Fill in **every** `CHANGE_ME_IN_PRODUCTION` value:

| Variable | What to put |
|---|---|
| `APP_KEY` | Run `php artisan key:generate --show` (or `docker run --rm -v "$PWD":/var/www -w /var/www php:8.2-cli php artisan key:generate --show`) — a valid base64 key |
| `APP_URL` | Public https domain, e.g. `https://bracketa.example.com` |
| `DB_PASSWORD` / `MYSQL_PASSWORD` | Strong, unique password (must match each other) |
| `MYSQL_ROOT_PASSWORD` | Strong, unique root password |
| `MAIL_*` | Production SMTP credentials |
| `AWS_*` | S3 credentials for backups (see §4) |
| `BACKUP_NOTIFICATION_EMAIL` | Ops email that receives backup failure/success alerts |

**Never commit `.env.production`** (it is gitignored). Never reuse the dev
`secret` credentials.

### 2.3 Build and start the stack

```bash
# 1) Compile front-end assets first — public/build is Vite output, NOT committed
npm ci
npm run build

# 2) Build and start the stack
docker compose -f docker-compose.prod.yml --env-file .env.production up -d --build
```

> `public/build` (Vite build output) is gitignored, so `npm run build` must run
> on the server before `up -d --build` — the compiled assets are baked into the
> images, they are never bind-mounted.

This starts: `app` (php-fpm), `nginx` (port 80 by default; override `APP_PORT`),
`mysql`, `redis`, `queue` (worker), `scheduler` (`schedule:work`).

> Code and nginx config are **baked into the images** (no bind mounts of the
> repo). `app`/`queue`/`scheduler` share `storage-data` (covers
> `storage/app/private`, `storage/app/public`, sessions, cache, logs) and
> `public-data` mounted at `public/uploads` (user-uploaded files), while `nginx`
> serves the same `public-data` volume so uploads are shared. Vite static assets
> are served from the baked image copy of `public/`. Rebuilding images is
> therefore how a new release ships — `up -d --build` in §6 is required, not
> optional. The mysql and redis services are gated by healthchecks
> (`condition: service_healthy`), so `app`/`queue`/`scheduler` only start once
> the databases are ready.

### 2.4 Migrations and setup

```bash
docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan storage:link
docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan config:cache
docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan route:cache
docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan view:cache
```

Seeding: run `db:seed --force` **only** on a brand-new empty database if seed data is
required (out of scope of normal deploys).

### 2.5 Verify

Two health endpoints exist — use both:

- **Liveness** — `curl -I http://<host>/up` → expect **HTTP 200**. This is Laravel's
  default framework health route (registered in `bootstrap/app.php`) and returns a
  plain **HTML "OK"** response — it does **not** return JSON.
- **Readiness** — `curl http://<host>/api/health` → expect **JSON** with
  `"status": "healthy"` and `services.database` / `services.cache` both `"healthy"`
  (registered in `routes/api.php`, returns `"degraded"` when a dependency fails).
- `docker compose -f docker-compose.prod.yml ps` — all services `Up`.
- `docker compose -f docker-compose.prod.yml logs --tail=50 scheduler` — no errors.

---

## 3. Backups (automated DB backup via spatie/laravel-backup)

- Runs daily via the scheduler container:
  - `02:00` — `php artisan backup:run --only-db` (database dump only)
  - `02:30` — `php artisan backup:clean` (retention: **7 daily + 4 weekly**)
- Destination disk: `s3` (override with `BACKUP_DESTINATION_DISK`), bucket from
  `AWS_BUCKET`. Backups are stored under `laravel-backup/<APP_NAME>/`.
- Failures/success send mail to `BACKUP_NOTIFICATION_EMAIL`.

### Manual backup

```bash
docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan backup:run --only-db
```

### List backups

```bash
docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan backup:list
```

### Restore from a backup

1. Download the wanted backup zip from S3 (or use the backup disk directly).
2. Extract it — it contains the `.sql` dump (plus any bundled files when not `--only-db`).
3. Restore the database:

```bash
docker compose -f docker-compose.prod.yml --env-file .env.production exec -T mysql sh -c \
  'exec mysql -utournatak -p"$MYSQL_PASSWORD" tournatak_laravel' < dump.sql
```

4. `php artisan cache:clear` (session/cache are DB-backed, so no other store to flush).

---

## 4. S3 prerequisites (backup destination)

- Create an S3 bucket (e.g. `bracketa-backups`) — enable **versioning** for extra safety.
- Create an IAM user with a policy allowing `s3:PutObject`, `s3:GetObject`,
  `s3:ListBucket`, `s3:DeleteObject` on that bucket (backup + cleanup need delete).
- Put the credentials in `.env.production` (`AWS_ACCESS_KEY_ID`,
  `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`).
- The `s3` disk is already defined in `config/filesystems.php`.

---

## 5. Queue & scheduler operations

- **Queue worker** (`queue` service): `php artisan queue:work --tries=3 --backoff=10`,
  `restart: always`. Scales horizontally by adding replicas.
- **Scheduler** (`scheduler` service): `php artisan schedule:work`, `restart: always`.
  Runs every minute in-process and dispatches due tasks.
- To run a one-off queued command:
  `docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan queue:monitor` (or any queue command).

---

## 6. Deploying a new release

```bash
cd /var/www/bracketa
git pull origin master
npm ci && npm run build   # recompile assets when the front-end changed
docker compose -f docker-compose.prod.yml --env-file .env.production up -d --build
docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan config:cache
```

> Prefer `php artisan down` / `up` for zero-downtime maintenance windows if the app
> cannot be kept live during migration.
>
> `up -d --build` **must** be used for every release — code is baked into the
> `app`/`queue`/`scheduler`/`nginx` images (no repo bind mounts), so this command
> is what ships the new code. Run `npm ci && npm run build` first so updated
> Vite assets are baked in too.

---

## 7. Validation / troubleshooting

| Check | Command |
|---|---|
| Compose file validity | `docker compose -f docker-compose.prod.yml --env-file .env.production config` |
| Liveness | `curl -I http://<host>/up` → HTTP 200 (plain "OK", no JSON) |
| Readiness | `curl http://<host>/api/health` → JSON `{"status":"healthy", ...}` with `services.database` / `services.cache` healthy |
| Service status | `docker compose -f docker-compose.prod.yml ps` |
| App logs | `docker compose -f docker-compose.prod.yml logs -f app` |
| Queue logs | `docker compose -f docker-compose.prod.yml logs -f queue` |
| Scheduler logs | `docker compose -f docker-compose.prod.yml logs -f scheduler` |
| Scheduled tasks | `docker compose -f docker-compose.prod.yml exec app php artisan schedule:list` |
| Backup status | `docker compose -f docker-compose.prod.yml exec app php artisan backup:monitor` |
| Backup notifications | configured to `BACKUP_NOTIFICATION_EMAIL` (mail channel) |

If the app container restarts, `restart: always` keeps the stack up; check
`docker compose -f docker-compose.prod.yml logs app` for the cause.
