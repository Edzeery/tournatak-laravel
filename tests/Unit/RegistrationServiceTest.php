<?php

namespace Tests\Unit;

use App\Models\Competition;
use App\Models\CompetitionDomain;
use App\Models\Team;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationServiceTest extends TestCase
{
    use RefreshDatabase;

    private RegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RegistrationService;
    }

    public function test_register_individual_creates_registration(): void
    {
        $competition = Competition::factory()->create();
        $user = User::factory()->create();

        $result = $this->service->registerIndividual($competition->id, $user->id);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('registrations', [
            'competition_id' => $competition->id,
            'user_id' => $user->id,
            'participant_type' => 'individual',
        ]);
    }

    public function test_register_individual_returns_error_on_duplicate(): void
    {
        $competition = Competition::factory()->create();
        $user = User::factory()->create();

        $this->service->registerIndividual($competition->id, $user->id);
        $result = $this->service->registerIndividual($competition->id, $user->id);

        $this->assertFalse($result['success']);
        $this->assertEquals(__('app.registration_already_exists'), $result['message']);
    }

    public function test_register_team_creates_registration(): void
    {
        $competition = Competition::factory()->create();
        $team = Team::factory()->create();

        $result = $this->service->registerTeam($competition->id, $team->id);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('registrations', [
            'competition_id' => $competition->id,
            'team_id' => $team->id,
            'participant_type' => 'team',
        ]);
    }

    public function test_register_team_returns_error_on_duplicate(): void
    {
        $competition = Competition::factory()->create();
        $team = Team::factory()->create();

        $this->service->registerTeam($competition->id, $team->id);
        $result = $this->service->registerTeam($competition->id, $team->id);

        $this->assertFalse($result['success']);
        $this->assertEquals(__('app.registration_team_already_exists'), $result['message']);
    }

    public function test_register_individual_with_custom_status(): void
    {
        $competition = Competition::factory()->create();
        $user = User::factory()->create();

        $result = $this->service->registerIndividual($competition->id, $user->id, 'pending');

        $this->assertTrue($result['success']);
        $this->assertEquals('pending', $result['registration']->status);
    }

    public function test_get_user_registrations_returns_individual_only(): void
    {
        $user = User::factory()->create();
        $competition = Competition::factory()->create();
        $this->service->registerIndividual($competition->id, $user->id);

        $registrations = $this->service->getUserRegistrations($user);

        $this->assertCount(1, $registrations);
        $this->assertEquals('individual', $registrations->first()->participant_type);
    }

    public function test_register_team_rejected_for_individual_only_domain(): void
    {
        $domain = CompetitionDomain::factory()->submission()->individual()->create();
        $competition = Competition::factory()->create(['domain_id' => $domain->id]);
        $team = Team::factory()->create();

        $result = $this->service->registerTeam($competition->id, $team->id);

        $this->assertFalse($result['success']);
        $this->assertEquals(__('app.registration_domain_participant_not_supported'), $result['message']);
        $this->assertDatabaseMissing('registrations', ['team_id' => $team->id]);
    }

    public function test_register_individual_allowed_for_individual_only_domain(): void
    {
        $domain = CompetitionDomain::factory()->submission()->individual()->create();
        $competition = Competition::factory()->create(['domain_id' => $domain->id]);
        $user = User::factory()->create();

        $result = $this->service->registerIndividual($competition->id, $user->id);

        $this->assertTrue($result['success']);
    }

    public function test_is_registration_allowed_for_sports_domain_allows_both(): void
    {
        $domain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();
        $competition = Competition::factory()->create(['domain_id' => $domain->id]);

        $this->assertTrue($this->service->isRegistrationAllowed($competition, 'individual'));
        $this->assertTrue($this->service->isRegistrationAllowed($competition, 'team'));
    }

    public function test_is_registration_allowed_for_individual_only_domain_rejects_teams(): void
    {
        $domain = CompetitionDomain::factory()->submission()->individual()->create();
        $competition = Competition::factory()->create(['domain_id' => $domain->id]);

        $this->assertTrue($this->service->isRegistrationAllowed($competition, 'individual'));
        $this->assertFalse($this->service->isRegistrationAllowed($competition, 'team'));
    }

    public function test_get_available_competitions_filters_by_domain_key(): void
    {
        $user = User::factory()->create();
        $sportsDomain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();
        $hackathonDomain = CompetitionDomain::where('slug', CompetitionDomain::SLUG_HACKATHON)->firstOrFail();
        $sportsCompetition = Competition::factory()->create([
            'domain_id' => $sportsDomain->id,
            'approval_status' => 'approved',
        ]);
        Competition::factory()->create([
            'domain_id' => $hackathonDomain->id,
            'approval_status' => 'approved',
        ]);

        $available = $this->service->getAvailableCompetitions($user, 'team', CompetitionDomain::SLUG_SPORTS);

        $this->assertCount(1, $available);
        $this->assertEquals($sportsCompetition->id, $available->first()->id);
    }
}
