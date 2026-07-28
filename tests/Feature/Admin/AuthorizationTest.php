<?php

namespace Tests\Feature\Admin;

use App\Models\Competition;
use App\Models\Match_;
use App\Models\Team;
use App\Models\TeamStaff;
use App\Models\User;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    public function test_admin_retains_full_access()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get('/panel/users')->assertStatus(200);
        $this->actingAs($admin)->get('/panel/teams')->assertStatus(200);
        $this->actingAs($admin)->get('/panel/competitions')->assertStatus(200);
        $this->actingAs($admin)->get('/panel/matches')->assertStatus(200);
        $this->actingAs($admin)->get('/panel/players')->assertStatus(200);
    }

    public function test_organizer_can_access_competitions_index()
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');

        $this->actingAs($organizer)->get('/panel/competitions')->assertStatus(200);
    }

    public function test_user_cannot_access_competitions_index()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)->get('/panel/competitions')->assertForbidden();
    }

    public function test_organizer_can_view_own_competition()
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $competition = Competition::factory()->create(['organizer_id' => $organizer->id]);

        $this->actingAs($organizer)->get("/panel/competitions/{$competition->id}/edit")->assertStatus(200);
    }

    public function test_organizer_cannot_view_other_competition()
    {
        $organizer1 = User::factory()->create();
        $organizer1->assignRole('organizer');
        $organizer2 = User::factory()->create();
        $organizer2->assignRole('organizer');

        $competition = Competition::factory()->create(['organizer_id' => $organizer2->id]);

        $this->actingAs($organizer1)->get("/panel/competitions/{$competition->id}/edit")->assertForbidden();
    }

    public function test_organizer_can_view_own_match()
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');

        $competition = Competition::factory()->create(['organizer_id' => $organizer->id]);
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();
        $match = Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
        ]);

        $this->actingAs($organizer)->get("/panel/matches/{$match->id}/edit")->assertStatus(200);
    }

    public function test_organizer_cannot_view_other_match()
    {
        $organizer1 = User::factory()->create();
        $organizer1->assignRole('organizer');
        $organizer2 = User::factory()->create();
        $organizer2->assignRole('organizer');

        $competition = Competition::factory()->create(['organizer_id' => $organizer2->id]);
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();
        $match = Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
        ]);

        $this->actingAs($organizer1)->get("/panel/matches/{$match->id}/edit")->assertForbidden();
    }

    public function test_coach_can_access_own_team_formations()
    {
        $coach = User::factory()->create();
        $coach->assignRole('coach');
        $team = Team::factory()->create();

        TeamStaff::create([
            'team_id' => $team->id,
            'user_id' => $coach->id,
            'staff_role' => 'head_coach',
            'is_active' => true,
        ]);

        $this->actingAs($coach)->get("/panel/teams/{$team->id}/formations")->assertStatus(200);
    }

    public function test_coach_cannot_access_other_team_formations()
    {
        $coach = User::factory()->create();
        $coach->assignRole('coach');
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();

        TeamStaff::create([
            'team_id' => $team1->id,
            'user_id' => $coach->id,
            'staff_role' => 'head_coach',
            'is_active' => true,
        ]);

        $this->actingAs($coach)->get("/panel/teams/{$team2->id}/formations")->assertForbidden();
    }

    public function test_unauthenticated_user_gets_redirect()
    {
        $team = Team::factory()->create();

        $this->get('/panel/teams')->assertRedirect();
        $this->get('/panel/matches')->assertRedirect();
        $this->get('/panel/competitions')->assertRedirect();
        $this->get('/panel/users')->assertRedirect();
    }

    public function test_unauthorized_user_gets_403()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)->get('/panel/teams')->assertForbidden();
        $this->actingAs($user)->get('/panel/matches')->assertForbidden();
        $this->actingAs($user)->get('/panel/competitions')->assertForbidden();
        $this->actingAs($user)->get('/panel/users')->assertForbidden();
        $this->actingAs($user)->get('/panel/players')->assertForbidden();
    }
}
