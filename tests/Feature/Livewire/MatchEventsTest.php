<?php

use App\Livewire\Admin\Matches\MatchEventsPage;
use App\Models\Match_;
use App\Models\MatchEvent;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create()->assignRole('admin');
    $this->team1 = Team::factory()->create();
    $this->team2 = Team::factory()->create();
    $this->match = Match_::factory()->create([
        'team1_id' => $this->team1->id,
        'team2_id' => $this->team2->id,
    ]);
});

test('can render events page', function () {
    $response = $this->actingAs($this->admin)
        ->get("/panel/matches/{$this->match->id}/events");

    $response->assertOk();
});

test('can create match event', function () {
    $player = Player::factory()->create(['team_id' => $this->team1->id]);

    Livewire::actingAs($this->admin)
        ->test(MatchEventsPage::class, ['match' => $this->match])
        ->set('eventForm.team_id', $this->team1->id)
        ->set('eventForm.player_id', $player->id)
        ->set('eventForm.event_type', 'goal')
        ->set('eventForm.minute', 23)
        ->call('saveEvent')
        ->assertStatus(200);

    $this->assertDatabaseHas('match_events', [
        'match_id' => $this->match->id,
        'player_id' => $player->id,
        'event_type' => 'goal',
        'minute' => 23,
    ]);
});

test('can delete match event', function () {
    $player = Player::factory()->create(['team_id' => $this->team1->id]);
    $event = MatchEvent::factory()->create([
        'match_id' => $this->match->id,
        'team_id' => $this->team1->id,
        'player_id' => $player->id,
        'event_type' => 'goal',
        'minute' => 10,
    ]);

    Livewire::actingAs($this->admin)
        ->test(MatchEventsPage::class, ['match' => $this->match])
        ->call('deleteEvent', $event->id)
        ->assertStatus(200);

    $this->assertDatabaseMissing('match_events', ['id' => $event->id]);
});

test('validation fails without required fields', function () {
    Livewire::actingAs($this->admin)
        ->test(MatchEventsPage::class, ['match' => $this->match])
        ->call('saveEvent')
        ->assertHasErrors([
            'eventForm.team_id',
            'eventForm.player_id',
            'eventForm.event_type',
            'eventForm.minute',
        ]);
});

test('can edit existing event', function () {
    $player = Player::factory()->create(['team_id' => $this->team1->id]);
    $event = MatchEvent::factory()->create([
        'match_id' => $this->match->id,
        'team_id' => $this->team1->id,
        'player_id' => $player->id,
        'event_type' => 'goal',
        'minute' => 10,
    ]);

    Livewire::actingAs($this->admin)
        ->test(MatchEventsPage::class, ['match' => $this->match])
        ->call('editEvent', $event->id)
        ->assertSet('showModal', true)
        ->assertSet('editingEventId', $event->id)
        ->assertSet('eventForm.minute', 10)
        ->set('eventForm.minute', 85)
        ->call('saveEvent');

    $this->assertDatabaseHas('match_events', [
        'id' => $event->id,
        'minute' => 85,
    ]);
});

test('guest cannot access events page', function () {
    $response = $this->get("/panel/matches/{$this->match->id}/events");
    $response->assertRedirect('/login');
});
