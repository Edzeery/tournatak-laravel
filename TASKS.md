# Tournatak → Multi-Domain Competition Platform (Phase Plan)

**Repo root (git):** `C:\laragon\www\tournatak-laravel`
**Baseline:** 226 tests passing (416 assertions) · Pint clean · PHPStan clean at level 1 (local only — **not yet in CI**) · 1130 unique translation keys per locale, all 4 locales **in sync** (ar=en=fr=es on unique keys).

> Prior cleanup plan (P0–P4) is complete; this plan supersedes it. Prior plan preserved in git history.

---

## Stage 1 — Discovery findings (verified against live code)

Confirmations:
- `Sport` is root: `competitions.sport_id`, `teams.sport_id`, `positions.sport_id` (all nullable FKs, backfilled to football). Sports seeded **inside** the migration `database/migrations/2026_07_30_161743_create_sports_table.php` (football, futsal).
- Sport-specific vocabulary: `Match_`, `MatchEvent`, `MatchLineup`, `Referee`, `Formation`, `TeamTactic`, `TeamMedicalRecord`, `StandingService`.
- Good foundations confirmed: `TournamentFormatService` (match-pairing strategies), `ScoringEngine` (concrete class — **not** an interface), `RegistrationService`, `CompetitionService`, PHP Enums, `status-badge`/`section-header`/`form-errors` components, `competition_profile` (official/casual), individual-participant support.

Deviations/corrections vs. the task brief (these shape the plan):
1. **DB ENUMs are forbidden for new schema.** The repo deliberately converted ENUM columns to `VARCHAR` (`2026_07_30_210000_convert_enum_columns_to_string.php`) because DBAL/ENUM migrations break on SQLite. → `competition_domains` will use **string columns** backed by new PHP enums (`CompetitionEvaluationBasis`, and reuse existing `ParticipantType` for `participant_basis`).
2. **PHPStan is not in CI.** `ci.yml` gates on composer audit, npm audit, Pint, tests (sqlite+mysql). → Add a PHPStan job in Phase 6 (and keep it clean in every phase).
3. **`competitions.type_id` / `subtype_id` are NOT NULL** (constrained FKs in `2026_07_26_210005`). Non-sports competitions cannot leave them null. → `CompetitionSetupService` will auto-provision a per-domain Type/Subtype via `firstOrCreate`, mirroring the existing casual-competition pattern (`CreateCasualCompetitionPage.php:28-42`).
4. **Domain rows must be seeded in the migration** (like sports) so the `competitions.domain_id` backfill works during `migrate:fresh`, **plus** a `CompetitionDomainSeeder` for re-seeding.
5. **`CompetitionService::create()` hardcodes football** as default sport (`app/Services/CompetitionService.php:21-23`) → becomes domain-aware (default domain = sports, sport default only applies within the sports domain).
6. **`CompetitionFactory` does not set `sport_id`/`domain_id`** → update factory to reference the seeded sports domain.
7. Tests for homepage (`tests/Feature/Public/HomePageTest.php`), competition pages (`CompetitionCrudTest.php`, `CompetitionDetailTest.php`) only assert HTTP 200 → Phase 3/4 redesigns must preserve those assertions.
8. Translation gap previously flagged for `es` is **already closed** (unique-key comparison). New strings still require all 4 locales.

End-state acceptance test (add a 6th domain): seed one `competition_domains` row + implement `ScoringEngineInterface` only if scoring is genuinely new + add domain tab content. No touching `Match_`, `MatchEvent`, or sports-domain code.

---

## Phase 1 (HIGH) — Backend Domain Model Foundation

### 1.1 `competition_domains` table + seed + enum
- **New files:**
  - `database/migrations/2026_08_01_000001_create_competition_domains_table.php`
    - `id, key (string, unique), name, name_en, name_fr, name_es, description, icon, participant_basis (string(20), default 'both'), evaluation_basis (string(20), default 'match'), is_active (bool), sort_order (unsignedSmallInt), timestamps`.
    - Seeds the 5 domains inline (like the sports migration): `sports`, `esports`, `academic_knowledge`, `hackathon_project`, `creative_arts` — each with all localized names/descriptions, an icon (bootstrap-icons, domain-neutral), participant_basis, evaluation_basis (`match` for sports/esports, `submission` for the other three), sort_order.
    - `down()`: `dropIfExists`.
  - `app/Enums/CompetitionEvaluationBasis.php` — `Match = 'match'`, `Submission = 'submission'`, with `label()`.
  - `app/Models/CompetitionDomain.php` — constants `KEY_SPORTS`/`KEY_ESPORTS`/`KEY_ACADEMIC`/`KEY_HACKATHON`/`KEY_CREATIVE`; `$fillable`, `$casts`; `localizedName()` (mirror `Sport::localizedName()`); `evaluationBasis()` and `participantBasis()` helpers returning enums; `isMatchBased()`/`isSubmissionBased()`; relationships `competitions(): HasMany`.
  - `database/factories/CompetitionDomainFactory.php` (lazy: `firstOrCreate`-backed state for `sports` key).
- **New seed file:** `database/seeders/CompetitionDomainSeeder.php` — `updateOrCreate` by `key`; call from `DatabaseSeeder::run()` (after RoleSeeder).
- **Implicit Sport↔domain link (no schema change):**
  - `app/Models/Sport.php`: add `domain(): CompetitionDomain` returning the `sports` domain (via `CompetitionDomain::where('key', KEY_SPORTS)`), and `competitionDomains(): Collection` (empty list fallback).
- **Unit tests:** `tests/Unit/CompetitionDomainTest.php` (seeded rows exist after `RefreshDatabase`, helpers, localizedName, relationships). `tests/Feature/Model/CompetitionDomainModelTest.php` for the Sport↔domain accessor.

### 1.2 `competitions.domain_id` + backfill
- **New file:** `database/migrations/2026_08_01_000002_add_domain_id_to_competitions_table.php`
  - Adds nullable `domain_id` FK → `competition_domains` (`nullOnDelete`), `after('organizer_id')`.
  - Backfill: `UPDATE competitions SET domain_id = <sports id>` (mirrors the sport_id backfill migration).
  - `down()`: drop FK + column.
- **Model:** `app/Models/Competition.php` — add `domain_id` to `$fillable`; `domain(): BelongsTo`; helpers `domainKey(): ?string`, `isSportsDomain(): bool`, `evaluationBasis(): ?CompetitionEvaluationBasis` (from domain, fallback `match` when null), `scopeDomain(string $key)`, `scopeInDomains(array $keys)`.
- **Factory:** `database/factories/CompetitionFactory.php` — set `domain_id` to the seeded sports domain (resolve via `CompetitionDomain` query, `afterCreating`/state-safe so migrate:fresh ordering holds).
- **Service:** `app/Services/CompetitionService.php` — `create()`: accept optional `domain_id`; when absent, resolve from `sport_id` (non-null ⇒ sports) else default to the `sports` domain; the football-sport default at lines 21-23 applies only when the resolved domain is `sports`.
- **Tests:** extend `tests/Unit/ModelTest.php` or new `tests/Feature/Model/CompetitionDomainTest.php` (competition→domain, domain→competitions, helpers, backfill via factory-created competition).

### 1.3 Generic evaluation-side models (submission domains)
- **New migrations (each with full `down()`):**
  - `2026_08_01_000003_create_competition_rounds_table.php` → `id, competition_id FK, round_number, name, start_date? , end_date?, meta (json nullable), timestamps`.
  - `2026_08_01_000004_create_submissions_table.php` → `id, competition_round_id FK, participant_type (string(20)), team_id?, user_id?, title?, content (text), file_path?, submitted_at?, status (string(20) default 'submitted'), timestamps` + indexes. (team_id/user_id mirrors `registrations` — no polymorphic id column.)
  - `2026_08_01_000005_create_judges_table.php` → `id, competition_id FK, user_id FK, role (string), timestamps`, unique `(competition_id, user_id)`.
  - `2026_08_01_000006_create_judge_scores_table.php` → `id, submission_id FK, judge_id FK (→ judges), criteria (string), score (decimal 5,2), comments?, timestamps`, unique `(submission_id, judge_id, criteria)`.
- **New enums:** `app/Enums/SubmissionStatus.php` — `Submitted`, `UnderReview`, `Scored`, `Rejected`.
- **New models + factories:**
  - `app/Models/CompetitionRound.php` (+ `CompetitionRoundFactory`) — `competition()`, `submissions()`.
  - `app/Models/Submission.php` (+ `SubmissionFactory`) — `round()`, `team()`, `user()`, `judgeScores()`, `averageScore()`, `totalScore()`, `isTeamSubmission()`/`isIndividualSubmission()` (mirror `Registration`).
  - `app/Models/Judge.php` (+ `JudgeFactory`) — `competition()`, `user()`, `scores()`.
  - `app/Models/JudgeScore.php` (+ `JudgeScoreFactory`) — `submission()`, `judge()`.
- **Do NOT touch** `Match_`, `MatchEvent`, `Referee` — they remain the `match`-evaluation implementation.
- **`ARCHITECTURE_NOTES.md`** (new file, repo root): section "Domain model" — the generic vs. domain-specific boundary, how to add a 6th domain, how to add a sport.

### 1.4 Policies — domain-agnostic top level
- `app/Policies/CompetitionPolicy.php` is already permission-based (domain-agnostic). Add a `judge(User, Competition)` ability checking membership in `judges` (used by Phase 4). Registration/Team policies stay as-is.
- New policies deferred to Phase 4 (`JudgePolicy`, `SubmissionPolicy`, `JudgeScorePolicy`) so Phase 1 ships no orphaned gates.

### 1.5 Phase 1 deliverables checklist
- [ ] 6 migrations, all additive, all with working `down()`
- [ ] 5 models + 5 factories + 1 enum + 1 seeded domain table + seeder wired into `DatabaseSeeder`
- [ ] `Competition`/`Sport`/`CompetitionService`/`CompetitionFactory` domain-aware (sports behavior byte-identical)
- [ ] Unit/feature tests for every new model/relationship + backfill
- [ ] `php artisan test` (226+new) · `vendor/bin/pint --test` · `php vendor/bin/phpstan analyse` all green
- [ ] `ARCHITECTURE_NOTES.md` created

---

## Phase 2 (HIGH) — Backend Service Layer Generalization

### 2.1 Scoring abstraction
- **New:** `app/Contracts/ScoringEngineInterface.php`
  - `supports(string $evaluationBasis): bool`
  - `calculateRanking(Competition $competition, array $context = []): array` — returns ranked participant rows.
- **New:** `app/Services/SportsScoringEngine.php` — implements interface; `supports('match')`; `calculateRanking()` delegates to existing `StandingService` (which keeps its concrete `ScoringEngine` dependency — **unchanged**, zero break).
- **New:** `app/Services/SubmissionScoringEngine.php` — implements interface; `supports('submission')`; aggregates `JudgeScore` per submission (average/total, count), returns ranked list.
- **New:** `app/Services/ScoringEngineRegistry.php` — resolves engine by `$competition->evaluationBasis()` (default: sports engine).
- Existing `app/Services/ScoringEngine.php` stays as the low-level sports points/tiebreaker utility (used by `StandingService`, `TournamentFormatService::getFormatConfig`). **No renames.**
- **Tests:** `tests/Unit/ScoringEngineRegistryTest.php`, `tests/Unit/SportsScoringEngineTest.php`, `tests/Unit/SubmissionScoringEngineTest.php`; existing `tests/Unit/ScoringEngineTest.php`/`StandingServiceTest.php` pass unchanged (proves abstraction didn't alter sports behavior).

### 2.2 `TournamentFormatService` — abstract "rounds" on top of match pairing
- `app/Services/TournamentFormatService.php`: add `generateRounds(Competition): array` (domain-neutral round descriptors: round_number, name, stage) derived from existing format config (`getFormatConfig`) — e.g. knockout ⇒ 1..N rounds, swiss ⇒ `swiss_rounds`, groups ⇒ group stage + knockout stage; and `createRounds(Competition): int` writing `CompetitionRound` rows (used by submission domains to define judging rounds; harmless/unused for match domains).
- Existing `generateMatches()`/`createMatches()` and all match-based strategies remain **exactly as-is**.
- **Tests:** extend `tests/Unit/TournamentFormatServiceTest.php` (round generation per format; match generation unchanged).

### 2.3 `RegistrationService` domain-awareness
- `app/Services/RegistrationService.php`: add `isRegistrationAllowed(Competition, string $participantType): bool` gating on the competition domain's `participant_basis` (sports domain = `both` ⇒ current behavior identical). Wire as a soft guard in `registerIndividual()`/`registerTeam()` returning the existing "not allowed" result shape (no exception throws that change current flows).
- `getAvailableCompetitions()` keeps filtering by `CompetitionType.participant_type` (sports flow unchanged) — add optional `?string $domainKey` param to filter by domain (used by Phase 3 public listing if needed).
- **Tests:** extend `tests/Unit/RegistrationServiceTest.php` and `tests/Feature/Livewire/RegistrationTest.php` (domain guard for a submission-domain competition).

### 2.4 `CompetitionSetupService` — single source of truth for the wizard
- **New:** `app/Services/CompetitionSetupService.php`
  - `stepsFor(CompetitionDomain): array` — ordered wizard steps per domain (sports: [domain, basics, sport+format, review]; hackathon: [domain, basics, rounds/judging-criteria, review]).
  - `fieldsFor(string $step, CompetitionDomain): array` — field descriptors (name, type, options, validation rules, required).
  - `validationFor(CompetitionDomain): array` — Laravel validation rules per domain step.
  - `provisionTypeFor(CompetitionDomain, array $data): [type_id, subtype_id]` — `firstOrCreate` per-domain type/subtype (mirrors casual-competition pattern at `CreateCasualCompetitionPage.php:28-42`).
- **New:** `app/Enums/CompetitionStep.php` (or plain string constants on the service — choose constants; avoid enum explosion).
- **Tests:** `tests/Unit/CompetitionSetupServiceTest.php` (sports step list matches current create form fields; hackathon step list has judging criteria; provisionType returns/create per domain).

### 2.5 Phase 2 deliverables checklist
- [x] Interface + 2 engines + registry, all unit-tested
- [x] Rounds generator added (match generation untouched)
- [x] Registration domain guard (sports behavior unchanged)
- [x] `CompetitionSetupService` + tests
- [x] Full safety net green (tests/Pint/PHPStan)

---

## Phase 3 (HIGH) — Homepage, Navigation & Information Architecture

### 3.1 Homepage — general competition platform
- `app/Livewire/Home/HomePage.php`: add `domains` (active `CompetitionDomain` list) + keep existing `stats`/`activeCompetitions`/`teams`.
- `resources/views/livewire/home/home-page.blade.php`:
  - Hero: domain-neutral headline/copy (new translation keys) — keep `hero-sports` container styling but swap vocabulary ("organize any competition", "participants and rounds").
  - New **domain showcase** section: one card per domain (icon, localized name, description, link to `route('competitions.index', ['domain' => $domain->key])`).
  - New **how-it-works** section (domain-neutral, 3 steps).
  - Keep Active Competitions section and Latest Teams section (teams section is sports-flavored; keep — sports is a first-class domain).
  - Preserve HTTP 200 (`HomePageTest`).
- **Tests:** extend `tests/Feature/Public/HomePageTest.php` (page 200; all 5 domain cards present).

### 3.2 Navigation
- `resources/views/layouts/app.blade.php`: add a **Domains** dropdown (or "Competitions" nav item with a domains flyout) linking to filtered listings; keep Home/Competitions/Teams/Matches/Players links (teams/matches/players are the sports hub, acceptable as sports-domain surfaces).
- `resources/views/layouts/admin.blade.php` sidebar: retitle the Competitions section to a domain-neutral hub; add "Domains" item (view-only, permission `manage settings` or new `manage domains` permission via `RoleSeeder` — add permission `manage domains` to admin role only, low-risk) linking to a new `Admin/CompetitionDomainsPage` (read-only listing seeded rows; Phase 3 scope: view list; no CRUD unless cheap).
  - New: `app/Livewire/Admin/CompetitionDomainsPage.php` + view + route `/panel/domains` (permission `manage settings`).
- **Tests:** extend `tests/Feature/Admin/CompetitionCrudTest.php` (domains page 200 for admin, 403 for plain user).

### 3.3 Public competitions listing — domain filter
- `app/Livewire/Public/CompetitionsPage.php`: read `?domain=` query; `scopeInDomains` filter; pass `domains` list + `activeDomain`.
- `resources/views/livewire/public/competitions-page.blade.php`: domain chips/filter bar above the grid; keep existing listing.
- **Tests:** `tests/Feature/Public/CompetitionsPageDomainFilterTest.php` (filter by domain returns only that domain's competitions; no filter returns all).

### 3.4 Dynamic page titles/breadcrumbs
- `CompetitionDetailPage` (public) + admin competition pages: breadcrumb/title labels adapt to domain vocabulary (e.g. submissions-domain pages use "Rounds/Submissions/Judging" strings). Backed by new translation keys; match-domain pages unchanged.

### 3.5 Competition creation wizard (domain-first)
- `app/Livewire/Admin/Competitions/CreateCompetitionPage.php` becomes step-driven:
  - Step 1: choose domain (from `competition_domains`, icons+descriptions).
  - Steps 2..n: fields driven by `CompetitionSetupService` (sports step reproduces the **current** create form 1:1 — same fields/validation/`CompetitionService::create()` call).
  - Keep route `admin.competitions.create` → this component (tests stay green).
- `resources/views/livewire/admin/competitions/create-competition-page.blade.php`: step indicator + dynamic field rendering; sports step visually identical to today.
- `CreateCasualCompetitionPage`: unchanged (casual path untouched), but `CompetitionService::create()` now stamps `domain_id` = sports.
- **Translations (4 locales):** domain names/descriptions, hero/how-it-works copy, wizard labels/step titles, domain-filter labels, nav labels, "judging/submissions" vocabulary.
- **Tests:** `tests/Feature/Livewire/CreateCompetitionWizardTest.php` — choose `sports` ⇒ produces official competition (type/subtype/sport preserved); choose `hackathon_project` ⇒ produces competition with domain_id + provisioned type/subtype; existing football create/edit flows asserted end-to-end unchanged.

### 3.6 Phase 3 deliverables checklist
- [ ] Homepage redesigned (domain showcase + how-it-works) — 200 preserved
- [ ] Public + admin navigation domain-aware
- [ ] Public listing domain filter
- [ ] Domain-first creation wizard (sports flow identical)
- [ ] Domains admin page (read-only)
- [ ] Translations ×4 · tests green

---

## Phase 4 (MEDIUM) — Domain-Specific Competition & Participant Pages

### 4.1 Generic detail shell + swapped tabs
- Public match-domain detail stays as-is (`app/Livewire/Public/CompetitionDetailPage.php` + `competition-detail-page.blade.php`).
- **New submission-domain detail:** `app/Livewire/Public/SubmissionCompetitionDetailPage.php` + `submission-competition-detail-page.blade.php`, registered via route slug logic (same `/competitions/{competition}` route — the controller-less Livewire route resolves to whichever component based on `evaluation_basis`; simplest non-breaking approach: keep the existing route/component and branch inside `CompetitionDetailPage::render()` to return the submission view when `$competition->evaluationBasis() === submission`).
  - Tabs: Overview · Rounds & Submissions · Results/Ranking (via `SubmissionScoringEngine`).
- Reuse `status-badge`, `section-header`, `empty-state`, `pagination`.

### 4.2 Admin submission + judging management
- `app/Livewire/Admin/Competitions/RoundsPage.php` + view — manage `CompetitionRound` rows (list/create).
- `app/Livewire/Admin/Competitions/SubmissionsPage.php` + view — list submissions per round, create/edit, set status.
- `app/Livewire/Admin/Competitions/CompetitionJudgingPage.php` + view — assign judges per round, view aggregated results (non-judge view).
- Routes under `admin.competitions.*` (permission `manage competitions`).

### 4.3 Judge experience
- `app/Livewire/Judge/JudgingPage.php` + view + route `/judge/competitions/{competition}` (auth + `JudgePolicy`):
  - sees only assigned submissions for the current round
  - enters scores per criteria (`judge-score-input` Blade component — new, minimal)
  - cannot see other judges' scores (configurable per competition via `format_config['judging']['hide_other_judges'] ?? true`)
  - results aggregate automatically via `SubmissionScoringEngine`.
- **Policies:** `app/Policies/JudgePolicy.php`, `SubmissionPolicy.php`, `JudgeScorePolicy.php`; register in `AppServiceProvider`; `CompetitionPolicy::judge()` from Phase 1 wired here.
- **Component:** `resources/views/components/judge-score-input.blade.php`.

### 4.4 End-to-end flow test
- `tests/Feature/Public/SubmissionCompetitionFlowTest.php`: create hackathon competition (domain `hackathon_project`) → create round → register team participants → submit submissions → assign judge → judge scores → `SubmissionScoringEngine` ranks → results page shows ranking. Plus a match-domain competition still renders the classic detail page (regression).

### 4.5 Phase 4 deliverables checklist
- [ ] Submission-domain public detail (tabs: overview/rounds/results)
- [ ] Admin rounds/submissions/judging management
- [ ] Judge scoring UI + policies + `judge-score-input` component
- [ ] End-to-end flow tests · translations ×4 · full safety net green

---

## Phase 5 (MEDIUM) — Visual Design System Rollout

### 5.1 Brand tokens (Deep Indigo `#1E1B4B` primary / Amber `#F5A622` accent)
- `resources/css/core/_variables.scss`: **add** (don't replace) `--brand-primary: #1E1B4B`, `--brand-primary-hover`, `--brand-accent: #F5A622`, `--brand-accent-glow`, `--brand-gradient`. Leave existing `--primary` (#ffc107 gold) untouched so sports UI is pixel-identical.
- `resources/css/components/_utilities.scss`: add `.text-brand`, `.bg-brand`, `.btn-brand`, `.btn-outline-brand` helpers.
- Apply tokens to **new/domain-neutral surfaces only**: homepage hero + domain cards + wizard + nav highlights + domain badge (`status-badge` gets a `domain` variant via existing `$variant` mechanism).
- Sports-domain pages keep sport icons (e.g. soccer ball) inside their own pages.

### 5.2 Bundle splitting / dynamic imports
- `resources/js/app.js`: baseline `npm run build` output sizes recorded before/after.
- Vite `resources/js/app.js` + `vite.config.js`: add dynamic `import()` for heavy domain JS — tactics board (`lineup-page`/`tactics`), judging interface — so the public bundle stays lean.

### 5.3 Visual QA notes
- `docs/visual-qa.md` (or section in ARCHITECTURE_NOTES.md): screenshots/checklist across homepage, wizard, one sports page, one hackathon page. Brand consistency check.

### 5.4 Phase 5 deliverables checklist
- [ ] Brand tokens added (sports visuals unchanged)
- [ ] Domain-neutral UI wired to tokens
- [ ] Dynamic imports + before/after bundle sizes
- [ ] Visual QA notes

---

## Phase 6 (LOW) — Hardening & Documentation

### 6.1 CI + regression
- `.github/workflows/ci.yml`: add PHPStan step (`vendor/bin/phpstan analyse`) after Pint.
- Fresh-install regression: `composer install`, `migrate:fresh --seed`, `npm run build`, full suite, Pint, PHPStan, `composer audit`, `npm audit` — all green.

### 6.2 Documentation
- `README.md`: rewrite as multi-domain competition platform — domain model, how to add a domain (6th-domain runbook), how to add a sport.
- `ARCHITECTURE_NOTES.md`: finalize domain model + extension points.
- `TASKS.md`: all checkboxes completed; record deviations from this plan and why.

---

## Deferred (out of scope — will not ship)

| Item | Reason |
|------|--------|
| Full `CompetitionType`/`Subtype` per-domain CRUD in admin | Overkill; setup service provisions types programmatically |
| `ScoringEngine` H2H tiebreaker | Requires full match-history design; unrelated to multi-domain |
| Judge score anonymization/blind judging UI | Configurable flag exists; UX iteration later |
| Submissions with file uploads | `file_path` column ships; storage driver integration later |
| esports-specific pages (lan / seeds) | Domain exists; esports reuses sports match engine + sports pages |
| Multi-tenant/orgs | Out of scope |

---

## Execution Order & Gates

```
Phase 1 → Phase 2 → Phase 3 → Phase 4 → Phase 5 → Phase 6
```
Each phase ends with: `php artisan test` (new + all 226), `vendor/bin/pint --test`, `php vendor/bin/phpstan analyse`, `php artisan view:cache`, and `composer audit`/`npm audit` (no new vulns). One commit per phase. Stop for review between phases.
