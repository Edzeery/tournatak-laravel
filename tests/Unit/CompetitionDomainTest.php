<?php

namespace Tests\Unit;

use App\Enums\CompetitionEvaluationBasis;
use App\Models\Competition;
use App\Models\CompetitionDomain;
use App\Models\CompetitionSubtype;
use App\Models\CompetitionType;
use App\Models\Sport;
use App\Models\User;
use App\Services\CompetitionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionDomainTest extends TestCase
{
    use RefreshDatabase;

    private function serviceData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Domain Competition',
            'type_id' => CompetitionType::factory()->create()->id,
            'subtype_id' => CompetitionSubtype::factory()->create()->id,
        ], $overrides);
    }

    public function test_domains_are_seeded_by_migration(): void
    {
        $domains = CompetitionDomain::orderBy('sort_order')->get();

        $this->assertCount(6, $domains);
        $this->assertSame(
            ['sports', 'esports', 'academic', 'hackathon', 'creative', 'design'],
            $domains->pluck('slug')->all(),
        );
    }

    public function test_evaluation_basis_is_cast_to_enum(): void
    {
        $sports = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();
        $academic = CompetitionDomain::where('slug', CompetitionDomain::SLUG_ACADEMIC)->firstOrFail();

        $this->assertInstanceOf(CompetitionEvaluationBasis::class, $sports->evaluation_basis);
        $this->assertTrue($sports->usesMatchEvaluation());
        $this->assertFalse($sports->usesSubmissionEvaluation());
        $this->assertTrue($academic->usesSubmissionEvaluation());
    }

    public function test_domain_helpers(): void
    {
        $sports = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();
        $academic = CompetitionDomain::where('slug', CompetitionDomain::SLUG_ACADEMIC)->firstOrFail();
        $design = CompetitionDomain::where('slug', CompetitionDomain::SLUG_DESIGN)->firstOrFail();

        $this->assertTrue($sports->isSports());
        $this->assertFalse($academic->isSports());
        $this->assertFalse($design->isSports());
    }

    public function test_design_domain_is_submission_based_and_supports_both_participants(): void
    {
        $design = CompetitionDomain::where('slug', CompetitionDomain::SLUG_DESIGN)->firstOrFail();

        $this->assertTrue($design->usesSubmissionEvaluation());
        $this->assertFalse($design->usesMatchEvaluation());
        $this->assertTrue($design->supportsTeams());
        $this->assertTrue($design->supportsIndividuals());
        $this->assertSame('Design', $design->localizedName('en'));
    }

    public function test_localized_name_falls_back_to_arabic(): void
    {
        $sports = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();

        $this->assertSame('الرياضات', $sports->localizedName('ar'));
        $this->assertSame('Sports', $sports->localizedName('en'));
        $this->assertSame('Deportes', $sports->localizedName('es'));
        $this->assertSame('الرياضات', $sports->localizedName('unknown'));
    }

    public function test_competition_belongs_to_domain(): void
    {
        $competition = Competition::factory()->create();

        $this->assertInstanceOf(CompetitionDomain::class, $competition->domain);
        $this->assertTrue($competition->domain->usesMatchEvaluation());
    }

    public function test_competition_sports_factory_state_uses_seeded_sports_domain(): void
    {
        $competition = Competition::factory()->sports()->create();

        $this->assertSame(CompetitionDomain::SLUG_SPORTS, $competition->domain->slug);
    }

    public function test_competition_submission_factory_state_uses_submission_domain(): void
    {
        $competition = Competition::factory()->submission()->create();

        $this->assertTrue($competition->usesSubmissionEvaluation());
        $this->assertFalse($competition->isSportsDomain());
    }

    public function test_competition_domain_helpers(): void
    {
        $sports = Competition::factory()->sports()->create();
        $submission = Competition::factory()->submission()->create();

        $this->assertTrue($sports->isSportsDomain());
        $this->assertFalse($sports->usesSubmissionEvaluation());
        $this->assertTrue($submission->usesSubmissionEvaluation());
        $this->assertFalse($submission->isSportsDomain());
    }

    public function test_sport_has_implicit_sports_domain(): void
    {
        $sport = Sport::firstOrFail();

        $this->assertSame(CompetitionDomain::SLUG_SPORTS, $sport->competitionDomain()->slug);
    }

    public function test_create_defaults_to_sports_domain_and_football(): void
    {
        $organizer = User::factory()->create();
        $this->actingAs($organizer);

        $competition = app(CompetitionService::class)->create($this->serviceData([
            'name' => 'Default Domain Competition',
        ]));

        $this->assertSame(CompetitionDomain::SLUG_SPORTS, $competition->domain->slug);
        $this->assertSame('football', $competition->sport->slug);
    }

    public function test_create_with_submission_domain_forces_sport_to_null(): void
    {
        $organizer = User::factory()->create();
        $this->actingAs($organizer);

        $domain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_ACADEMIC)->firstOrFail();

        $competition = app(CompetitionService::class)->create($this->serviceData([
            'name' => 'Academic Competition',
            'domain_id' => $domain->id,
        ]));

        $this->assertSame(CompetitionDomain::SLUG_ACADEMIC, $competition->domain->slug);
        $this->assertNull($competition->sport_id);
    }

    public function test_create_with_explicit_sports_domain_keeps_football_default(): void
    {
        $organizer = User::factory()->create();
        $this->actingAs($organizer);

        $domain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();

        $competition = app(CompetitionService::class)->create($this->serviceData([
            'name' => 'Sports Competition',
            'domain_id' => $domain->id,
        ]));

        $this->assertSame('football', $competition->sport->slug);
    }
}
