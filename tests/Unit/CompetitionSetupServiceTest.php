<?php

namespace Tests\Unit;

use App\Models\CompetitionDomain;
use App\Services\CompetitionSetupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionSetupServiceTest extends TestCase
{
    use RefreshDatabase;

    private CompetitionSetupService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CompetitionSetupService;
    }

    public function test_steps_for_sports_domain_match_create_form_flow(): void
    {
        $domain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();

        $steps = $this->service->stepsFor($domain);

        $this->assertEquals(
            [CompetitionSetupService::STEP_DOMAIN, CompetitionSetupService::STEP_BASICS, CompetitionSetupService::STEP_FORMAT, CompetitionSetupService::STEP_REVIEW],
            $steps
        );

        $fields = array_column($this->service->fieldsFor(CompetitionSetupService::STEP_BASICS, $domain), 'name');
        $this->assertContains('name', $fields);
        $this->assertContains('description', $fields);
        $this->assertContains('location', $fields);
        $this->assertContains('start_date', $fields);
        $this->assertContains('end_date', $fields);
    }

    public function test_steps_for_hackathon_domain_include_rounds_with_judging(): void
    {
        $domain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_HACKATHON)->firstOrFail();

        $steps = $this->service->stepsFor($domain);

        $this->assertEquals(
            [CompetitionSetupService::STEP_DOMAIN, CompetitionSetupService::STEP_BASICS, CompetitionSetupService::STEP_ROUNDS, CompetitionSetupService::STEP_REVIEW],
            $steps
        );

        $fields = array_column($this->service->fieldsFor(CompetitionSetupService::STEP_ROUNDS, $domain), 'name');
        $this->assertContains('rounds_count', $fields);
        $this->assertContains('judging_criteria', $fields);
    }

    public function test_fields_for_unknown_step_return_empty(): void
    {
        $domain = CompetitionDomain::factory()->create();

        $this->assertSame([], $this->service->fieldsFor('unknown', $domain));
    }

    public function test_validation_for_sports_domain_aggregates_rules(): void
    {
        $domain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();

        $rules = $this->service->validationFor($domain);

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('domain_id', $rules);
        $this->assertArrayHasKey('sport_id', $rules);
        $this->assertArrayHasKey('format', $rules);
    }

    public function test_validation_for_submission_domain_has_rounds_rules(): void
    {
        $domain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_HACKATHON)->firstOrFail();

        $rules = $this->service->validationFor($domain);

        $this->assertArrayHasKey('rounds_count', $rules);
        $this->assertArrayHasKey('judging_criteria', $rules);
        $this->assertArrayNotHasKey('sport_id', $rules);
    }

    public function test_provision_type_for_creates_and_reuses_type_and_subtype(): void
    {
        $domain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_HACKATHON)->firstOrFail();

        $result = $this->service->provisionTypeFor($domain);

        $this->assertDatabaseHas('competition_types', ['id' => $result['type_id']]);
        $this->assertDatabaseHas('competition_subtypes', ['id' => $result['subtype_id']]);

        $second = $this->service->provisionTypeFor($domain);

        $this->assertSame($result, $second);
    }

    public function test_provision_type_for_uses_domain_participant_basis(): void
    {
        $domain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_ACADEMIC)->firstOrFail();

        $result = $this->service->provisionTypeFor($domain);

        $this->assertDatabaseHas('competition_types', [
            'id' => $result['type_id'],
            'participant_type' => 'individual',
        ]);
    }

    public function test_provision_type_for_honors_overrides(): void
    {
        $domain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_HACKATHON)->firstOrFail();

        $result = $this->service->provisionTypeFor($domain, [
            'type_name' => 'Innovation Sprint',
            'participant_type' => 'team',
        ]);

        $this->assertDatabaseHas('competition_types', [
            'id' => $result['type_id'],
            'name' => 'Innovation Sprint',
            'participant_type' => 'team',
        ]);
    }
}
