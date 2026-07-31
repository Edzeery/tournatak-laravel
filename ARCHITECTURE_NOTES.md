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
