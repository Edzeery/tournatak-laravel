<?php

namespace Tests\Unit;

use App\Models\Competition;
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
}
