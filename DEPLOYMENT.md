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
docker compose -f docker-compose.prod.yml --env-file .env.production up -d --build
```

This starts: `app` (php-fpm), `nginx` (port 80 by default; override `APP_PORT`),
`mysql`, `redis`, `queue` (worker), `scheduler` (`schedule:work`).

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

- Health endpoint: `curl http://<host>/up` → `{"status":"ok"}` (route registered in
  `bootstrap/app.php`).
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
docker compose -f docker-compose.prod.yml --env-file .env.production up -d --build
docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan config:cache
```

> Prefer `php artisan down` / `up` for zero-downtime maintenance windows if the app
> cannot be kept live during migration.

---

## 7. Validation / troubleshooting

| Check | Command |
|---|---|
| Compose file validity | `docker compose -f docker-compose.prod.yml --env-file .env.production config` |
| Service status | `docker compose -f docker-compose.prod.yml ps` |
| App logs | `docker compose -f docker-compose.prod.yml logs -f app` |
| Queue logs | `docker compose -f docker-compose.prod.yml logs -f queue` |
| Scheduler logs | `docker compose -f docker-compose.prod.yml logs -f scheduler` |
| Scheduled tasks | `docker compose -f docker-compose.prod.yml exec app php artisan schedule:list` |
| Backup status | `docker compose -f docker-compose.prod.yml exec app php artisan backup:monitor` |
| Backup notifications | configured to `BACKUP_NOTIFICATION_EMAIL` (mail channel) |

If the app container restarts, `restart: always` keeps the stack up; check
`docker compose -f docker-compose.prod.yml logs app` for the cause.
