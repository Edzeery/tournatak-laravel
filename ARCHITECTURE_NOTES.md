# Architecture Notes

## Competition Domains (multi-domain model)

Tournatak was originally a sports-only platform rooted in `Sport`. The
multi-domain model introduces `CompetitionDomain` as a top-level grouping so
the same `Competition` core can power sports, esports, academic/quiz,
hackathon, and creative-arts competitions.

### Domain model

```
CompetitionDomain (1) ──< (n) Competition (1) ──< (n) Registration
      │                                  ├──< (n) Match_      (match-based evaluation)
      │                                  ├──< (n) CompetitionRound (1) ──< (n) Submission (1) ──< (n) JudgeScore
      │                                  └──< (n) Judge
      │
      └── Sport (implicit link: sports always map to the "sports" domain)
```

- `competition_domains.slug` is the stable identity (sports, esports,
  academic, hackathon, creative).
- `competition_domains.evaluation_basis` is a string column (`match` |
  `submission`) cast to `App\Enums\CompetitionEvaluationBasis`. No DB ENUMs —
  see the repo-wide ENUM→VARCHAR precedent in
  `2026_07_30_210000_convert_enum_columns_to_string.php`.
- `competitions.domain_id` is a **nullable** FK (`nullOnDelete`). Existing
  rows were backfilled to the `sports` domain in
  `2026_08_01_000002_add_domain_id_to_competitions_table.php`.
- `competitions.sport_id` stays **non-null for sports** and is `null` for
  non-sports domains. `Competition::isSportsDomain()` and
  `Competition::usesSubmissionEvaluation()` are the domain-aware helpers.
- Sports have an **implicit** link: `Sport::competitionDomain()` returns the
  `sports` domain row (there is no `sports.domain_id` column).

### Seeding

Domains are seeded **inside the create migration**
(`2026_08_01_000001_create_competition_domains_table.php`) so that the
`domain_id` backfill works on `migrate:fresh`. `CompetitionDomainSeeder`
(`updateOrCreate` by slug, idempotent) is also wired into `DatabaseSeeder`
for production installs and updates.

### Domain-aware service behavior

`CompetitionService::create()` resolves the domain:

- No `domain_id` given → defaults to the `sports` domain (preserves legacy
  football behavior, including the implicit `sport_id = football` default).
- Non-sports domain → `sport_id` is forced to `null`.

`CompetitionFactory` defaults to a match-evaluation domain; `sports()` and
`submission()` states select a real seeded domain by slug/evaluation basis.

### Evaluation side (added in Phase 1, exercised in Phases 2–4)

- `competition_rounds` — named, numbered phases of a submission-based
  competition (status is a string: scheduled | in_progress | completed |
  cancelled).
- `submissions` — participant deliverables (team or individual), optionally
  tied to a round, with a pending | under_review | approved | rejected status
  (`App\Enums\SubmissionStatus`).
- `judges` — users assigned to judge a competition (`is_lead` marks the lead
  judge; unique per competition+user).
- `judge_scores` — one score + optional notes per judge per submission (unique
  per submission+judge).

### Authorization

`CompetitionPolicy::judge()` grants access to judging UI for: assigned
judges, the owning organizer, and any user with `manage competitions`.

### Phase 2 — service-layer generalization

#### `participant_basis`

`competition_domains.participant_basis` is a string column (`team` |
`individual` | `both`, default `both`) added by
`2026_08_01_000007_add_participant_basis_to_competition_domains_table.php`,
which backfills seeded domains **by slug** (academic = `individual`, the rest
`both`). `CompetitionDomain::supportsTeams()` / `supportsIndividuals()` are
the domain-aware helpers. The domain factory, seeder, and step tests all use
these values.

#### Scoring abstraction (2.1)

- `app/Contracts/ScoringEngineInterface.php` — `supports(string $evaluationBasis)`
  and `calculateRanking(Competition, array $context = []): array`.
- `app/Services/SportsScoringEngine.php` — `supports('match')`; delegates
  `calculateRanking()` to the existing `StandingService` (unchanged).
- `app/Services/SubmissionScoringEngine.php` — `supports('submission')`;
  aggregates `JudgeScore` per submission (average/total/min/max + count) and
  returns a ranked list; per-competition `scoring` config in `format_config`
  (`max_score`, `aggregation`) overlays `SubmissionScoringEngine::DEFAULT_CONFIG`.
- `app/Services/ScoringEngineRegistry.php` — resolves an engine from
  `$competition->evaluationBasis()` (unknown basis falls back to the default /
  first-registered engine). Registered in `AppServiceProvider` as a singleton
  with the two stable engines (no dynamic DB lookups at registration).
- The original `app/Services/ScoringEngine.php` remains the low-level
  sports points/tiebreaker utility (used by `StandingService` and
  `TournamentFormatService::getFormatConfig`). No renames.

#### Rounds generator (2.2)

`TournamentFormatService::generateRounds(Competition)` returns domain-neutral
round descriptors (`round_number`, `name`, `stage`) derived from
`getFormatConfig` (knockout ⇒ 1..N, swiss ⇒ `swiss_rounds`, groups ⇒ group +
knockout stages, home_away/double_elimination ⇒ numbered rounds).
`createRounds(Competition)` persists `CompetitionRound` rows idempotently
(skips existing round numbers) and is used by submission domains to define
judging rounds; match generation is untouched.

#### Registration domain guard (2.3)

`RegistrationService::isRegistrationAllowed(Competition, string $participantType)`
gates on the competition **domain's** `participant_basis` only (sports = `both`
⇒ legacy behavior identical). It is wired as a soft guard in
`registerIndividual()`/`registerTeam()` returning the standard
`['success' => false, 'message' => ...]` shape. `getAvailableCompetitions()`
gains an optional `?string $domainKey` filter (`whereHas('domain')`) while
keeping its `CompetitionType.participant_type` filter.

#### Competition setup wizard (2.4)

`app/Services/CompetitionSetupService.php` is the single source of truth for
the create-competition wizard:

- `stepsFor(CompetitionDomain)` — match domains: `[domain, basics, format,
  review]`; submission domains: `[domain, basics, rounds, review]`.
- `fieldsFor(string $step, CompetitionDomain)` — field descriptors (`name`,
  `label`, `type`, `options`, `validation`, `required`). The sports `basics`
  step mirrors the current `CreateCompetitionPage` fields; the `format` step
  adds `sport_id` + `format`; the `rounds` step adds `rounds_count` +
  `judging_criteria`.
- `validationFor(CompetitionDomain)` — aggregates the per-field rules into one
  Laravel rules array per domain.
- `provisionTypeFor(CompetitionDomain, array $data)` — `firstOrCreate` of a
  per-domain subtype (`General <Domain>`) and type (`general-<slug>`), honoring
  `participant_basis` and optional `type_name`/`participant_type` overrides.
  Returns `['type_id' => ..., 'subtype_id' => ...]` because
  `competitions.type_id`/`subtype_id` are NOT NULL.

Step/field labels use `app.*` keys present in all four locale files
(`step_domain`, `step_basics`, `step_format`, `step_rounds`, `step_review`,
`domain`, `format`, `rounds_count`, `judging_criteria`, `general_competition_type`,
`round`, `round_group_stage`, `round_knockout_stage`).

### Phase 3 — Homepage, navigation & information architecture

- **Homepage** (`Home/HomePage`): hero copy is domain-neutral (`home_hero_*`
  keys); new **domain showcase** section renders one card per active
  `CompetitionDomain` (icon, `localizedName()`, description, link to
  `route('competitions.index', ['domain' => $domain->slug])`); new
  **how-it-works** section (3 domain-neutral steps).
- **Public nav** (`layouts/app.blade.php`): a **Domains** dropdown lists active
  domains and links to filtered `/competitions?domain={slug}`; mobile offcanvas
  gained a matching sub-list. The "Competitions" nav item's active class now
  excludes `?domain` so filtered pages highlight the Domains dropdown instead.
- **Admin nav** (`layouts/admin.blade.php`): sidebar section retitled
  "Competitions & Domains"; read-only **Domains** page at `/panel/domains`
  (`Admin/CompetitionDomainsPage`, permission `manage settings`) listing seeded
  rows with competition counts. No CRUD in Phase 3.
- **Domain filter** (`Public/CompetitionsPage`): `#[Url(as: 'domain')]` +
  `Competition::scopeInDomains()` (whereHas domain slug); filter chip bar in the
  listing; `activeDomain` drives the hero badge.
- **Dynamic vocabulary**: the public detail hero badge and admin competition
  index/edit pages show the domain badge and use submission-vocabulary keys
  (`rounds`, `submissions`, `judging`, `submission_domain_manage_hint`) for
  submission-based competitions; match-domain pages are unchanged.
- **Create wizard** (`Admin/Competitions/CreateCompetitionPage`): step-driven,
  route unchanged (`admin.competitions.create`). Step 1 = domain cards;
  sports flow = `[domain, basics, review]` where "basics" reproduces the
  previous single form 1:1 (name/type/subtype/location/dates/description +
  `CompetitionService::create()` + `getValidationRules()`); submission flow =
  `[domain, basics, rounds, review]` driven by `CompetitionSetupService`
  (rounds step = `rounds_count` + `judging_criteria`, stored in
  `format_config`) with per-domain type/subtype provisioned via
  `provisionTypeFor()`. Stepper + review summary; flatpickr on datetime fields.

### Phase 4 -- submission competition pages & judging

- **Public detail branch** (`Public/CompetitionDetailPage`): `render()` keeps
  the match-domain page untouched and branches to a dedicated submission view
  (`livewire.public.submission-competition-detail-page`) when
  `$competition->evaluationBasis() === 'submission'`. The view renders a hero,
  stats panel (rounds/submissions/judges), and Bootstrap tabs: **Overview**,
  **Rounds & Submissions**, **Results** (ranking via
  `SubmissionScoringEngine`, only shown once `scores_count > 0`).
- **Admin management** (all under the `admin.` prefix + `manage competitions`
  permission, routes `admin.competitions.{rounds,submissions,judging}`):
  - `Admin/Competitions/RoundsPage` -- list + create `CompetitionRound` rows
    (`nextRoundNumber()` = max number + 1; duplicate `[competition_id, number]`
    guarded).
  - `Admin/Competitions/SubmissionsPage` -- submissions filtered by
    `?round=`; create for team or individual participants; inline edit
    (title/description/status); quick `setStatus()` from the list.
  - `Admin/Competitions/CompetitionJudgingPage` -- assign/remove judges
    (duplicate assignment guarded), toggle `hide_other_judges` persisted in
    `format_config['judging']['hide_other_judges'] ?? true`, live ranking table
    via the scoring engine.
- **Judge panel** (`Judge/JudgingPage` at `/judge/competitions/{competition}`,
  auth only -- no permission middleware; authorization via
  `CompetitionPolicy::judge()` which covers assigned judges, the owning
  organizer, and `manage competitions` holders). Shows the current (in-progress
  or first upcoming) round with a round switcher; judges enter per-submission
  scores via the `judge-score-input` Blade component (bound to
  `scores.{submissionId}.{score,notes}`, validated against the engine's
  `maxScore()`); other judges' averages are hidden unless the organizer
  disables `hide_other_judges`.
- **Policies** (registered in `AppServiceProvider`):
  - `JudgePolicy` -- `create`/`delete` proxy to `Competition::update`.
  - `SubmissionPolicy` -- `update`/`delete` proxy to `Competition::update`.
  - `JudgeScorePolicy` -- `view`/`update` allow admins (via `before`), the
    owning organizer, and the score's owning judge (cross-competition scores
    are rejected).
- **Scoring**: the ranking table reuses `SubmissionScoringEngine`
  (`calculateRanking()`, `maxScore()`, aggregation from
  `format_config['scoring']`); judges only ever persist rows for their own
  `Judge` (first-or-create per competition+user).

### Phase 5 -- visual design system rollout

- **Brand tokens** (`resources/css/core/_variables.scss`, light block only so
  dark mode inherits): `--brand-primary` (deep indigo `#1e1b4b`),
  `--brand-primary-hover`/`--brand-primary-soft`, `--brand-accent` (amber
  `#f5a622`), `--brand-accent-soft`/`--brand-accent-glow`,
  `--brand-text-on-accent`, `--brand-gradient` (indigo 135deg). `--primary`
  gold and everything feeding the sports UI are untouched and stay
  pixel-identical.
- **Utilities** (`components/_utilities.scss`): `.text-brand`, `.bg-brand`,
  `.bg-brand-soft`, `.border-brand`, `.btn-brand` (indigo gradient pill),
  `.btn-outline-brand` (amber outline pill); plus `.badge-domain`
  (`components/_badges.scss`, amber tint on transparent so it reads on both
  light surfaces and the dark indigo hero).
- **Wiring -- domain-neutral surfaces only**:
  - `--gradient-hero` now resolves to `var(--brand-gradient)`; homepage hero
    `::before`/`::after` glows and `.hero-badge`/`.hero-shape` switched to
    amber/indigo tones (`pages/_hero.scss`); hero title span, hero stat
    numbers, and domain-card icon/button use brand classes
    (`livewire/home/home-page.blade.php`).
  - Nav highlight states (`.nav-link:hover`/`.active` + underline,
    mobile `.nav-link-mobile.active`, `.offcanvas-lang-btn.active`,
    `.navbar-role-badge`) use `--brand-accent`
    (`layouts/_navbar.scss`); gold avatar buttons stay gold.
  - Create-competition wizard stepper (active = `btn-brand`, done =
    `btn-outline-brand`), continue/save buttons, and selected domain-card
    border (`border-brand`) are branded.
  - Domain badges on `admin/competitions/competitions-page`,
    `edit-competition-page`, and the submission detail overview tab now use
    `.badge-domain`. `badge-sport` remains for stats/rounds/points badges.
- **Bundle splitting** (`resources/js/app.js` + `vite.config.js`):
  - ApexCharts is no longer eagerly bundled. `window.loadApexCharts()` does a
    one-time `import('apexcharts')` and caches the promise; the admin
    dashboard's `@push('scripts')` init awaits it before `new ApexCharts(...)`.
  - The lineup board Alpine component moved from an inline `<script>` to its
    own Vite entry `resources/js/lineup.js` (registered via `alpine:init` +
    `Alpine.data('lineupInteractions', ...)`), loaded only on
    `admin/matches/lineup-page`.
  - **Before → after** (`npm run build`): main `app.js` 1098.72 kB → 266.52 kB
    (gzip 318.29 → 80.34 kB, ~76% smaller). ApexCharts ships as an on-demand
    chunk (832.50 kB / gzip 238.36 kB) loaded only on the admin dashboard;
    `lineup.js` is 0.53 kB.

### 5.3 Visual QA checklist (Phase 5)

- Homepage (public, light + dark): hero renders indigo gradient, amber
  badge/title accent + stat numbers, gold CTAs still legible on indigo.
- Competition wizard: active step + continue buttons show indigo gradient,
  done steps amber outline, selected domain card has amber border.
- Navbar (public + admin): hover/active links + active underline amber;
  role badge amber; avatar stays gold.
- Domain badge on admin competitions list/edit + submission detail hero: amber
  pill readable on both the light card and the dark hero.
- One sports page (e.g. match-domain competition detail): visuals unchanged
  (gold badges, `btn-primary-sport`, status-kit variants).
- One hackathon page (`/competitions?domain=hackathon`, submission detail):
  domain badge/detail reads correctly; charts on admin dashboard still render
  after the lazy ApexCharts change.

### Adding a 6th domain (runbook)

1. Add the row to `2026_08_01_000001_create_competition_domains_table.php`
   and to `CompetitionDomainSeeder` (same data), including the
   `evaluation_basis` and `participant_basis` values.
2. Add the slug constant to `CompetitionDomain::SLUGS`.
3. If the new domain uses match evaluation, reuse existing match generation
   (`TournamentFormatService`); otherwise reuse the submission/rounds flow.
   The scoring registry resolves the engine automatically from
   `evaluation_basis`.
4. Add a `domain_<slug>` nav item / homepage tile if it should surface in UI.
5. Add translations for any new labels to all four locales (ar, en, fr, es)
   — all locale files must stay in sync.
6. Run `migrate:fresh --seed`, the full test suite, Pint, and PHPStan.

### Extension points (finalized, Phase 6)

The platform is deliberately extended by **data + a small, well-known set of
hooks**, never by touching sports-domain code:

| Concern | Where to extend | Notes |
|---|---|---|
| New domain | `competition_domains` row (migration + seeder) + `CompetitionDomain::SLUGS` | localized names/descriptions required in all 4 locales |
| New evaluation basis | `CompetitionEvaluationBasis` enum + a `ScoringEngineInterface` implementation registered in `AppServiceProvider` | registry falls back to the first engine; no code in `Match_`/`Submission` changes |
| Wizard steps/fields | `CompetitionSetupService::stepsFor()` / `fieldsFor()` | match domains: `[domain, basics, format, review]`; submission: `[domain, basics, rounds, review]` |
| Per-domain type/subtype | auto-provisioned by `CompetitionSetupService::provisionTypeFor()` | keeps `competitions.type_id`/`subtype_id` (NOT NULL) satisfied |
| New sport | `sports` rows + `positions` rows | belongs to the sports domain only |
| Vocab / labels | `app.*` translation keys in all 4 locale files | ar/en/fr/es must stay key-synced |
| Public filtering | `Competition::scopeInDomains()` + `?domain=` on `competitions.index` | already wired for any new slug |
| Visual identity | `--brand-*` tokens (`_variables.scss`) + `.text-brand`/`.btn-brand`/`.badge-domain` utilities | gold sports identity (`--primary`) stays untouched |
| JS weight | dynamic `import()` chunks + page-scoped Vite entries | see Phase 5 bundle-split notes |

The end-state acceptance test (from the plan) is still the guardrail: add a 6th
domain by seeding one row + optional engine — with **no** changes to
`Match_`, `MatchEvent`, or sports-domain code.
