<?php

namespace App\Livewire\Admin\Matches;

use App\Models\Match_;
use App\Models\Player;
use App\Services\MatchService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class MatchControlPage extends Component
{
    public Match_ $match;

    public int $score1 = 0;

    public int $score2 = 0;

    public int $addedTime1 = 0;

    public int $addedTime2 = 0;

    public int $addedTimeET1 = 0;

    public int $addedTimeET2 = 0;

    public string $eventMinute = '';

    public string $eventDescription = '';

    public Collection $team1Players;

    public Collection $team2Players;

    public ?int $selectedPlayerId = null;

    public bool $supportsET = false;

    public function mount(Match_ $match)
    {
        $this->match = $match->load([
            'team1', 'team2', 'competition',
            'events' => fn ($q) => $q->with('player', 'team')->latest('minute'),
        ]);

        $this->score1 = $match->score_team1 ?? 0;
        $this->score2 = $match->score_team2 ?? 0;
        $this->addedTime1 = $match->added_time_first_half ?? 0;
        $this->addedTime2 = $match->added_time_second_half ?? 0;
        $this->addedTimeET1 = $match->extra_data['added_time_et_first_half'] ?? 0;
        $this->addedTimeET2 = $match->extra_data['added_time_et_second_half'] ?? 0;
        $this->supportsET = $match->competition?->format_config['extra_time'] ?? false;

        $this->loadPlayers();
    }

    protected function loadPlayers(): void
    {
        $service = app(MatchService::class);
        $this->team1Players = $service->getLineupPlayers($this->match, $this->match->team1_id);
        $this->team2Players = $service->getLineupPlayers($this->match, $this->match->team2_id);
    }

    // ── Phase transitions ──────────────────────────────────────────

    public function startFirstHalf()
    {
        app(MatchService::class)->transitionPhase($this->match, Match_::PHASE_FIRST_HALF);
        $this->refresh();
        $this->dispatch('swal:success', message: __('app.first_half_started'));
    }

    public function endFirstHalf()
    {
        app(MatchService::class)->transitionPhase($this->match, Match_::PHASE_HALF_TIME);
        $this->refresh();
        $this->dispatch('swal:info', message: __('app.first_half_ended'));
    }

    public function startSecondHalf()
    {
        app(MatchService::class)->transitionPhase($this->match, Match_::PHASE_SECOND_HALF);
        $this->refresh();
        $this->dispatch('swal:success', message: __('app.second_half_started'));
    }

    public function endSecondHalf()
    {
        if ($this->supportsET) {
            app(MatchService::class)->transitionPhase($this->match, Match_::PHASE_ET_BREAK);
            $this->dispatch('swal:info', message: __('app.second_half_ended'));
        } else {
            app(MatchService::class)->transitionPhase($this->match, Match_::PHASE_FULL_TIME);
            $this->dispatch('swal:success', message: __('app.match_ended'));
        }

        $this->refresh();
    }

    public function startETFirstHalf()
    {
        app(MatchService::class)->transitionPhase($this->match, Match_::PHASE_ET_FIRST_HALF);
        $this->refresh();
        $this->dispatch('swal:success', message: __('app.et_first_half_started'));
    }

    public function endETFirstHalf()
    {
        app(MatchService::class)->transitionPhase($this->match, Match_::PHASE_ET_HALF_TIME);
        $this->refresh();
        $this->dispatch('swal:info', message: __('app.et_first_half_ended'));
    }

    public function startETSecondHalf()
    {
        app(MatchService::class)->transitionPhase($this->match, Match_::PHASE_ET_SECOND_HALF);
        $this->refresh();
        $this->dispatch('swal:success', message: __('app.et_second_half_started'));
    }

    public function endMatch()
    {
        app(MatchService::class)->transitionPhase($this->match, Match_::PHASE_FULL_TIME);
        $this->refresh();
        $this->dispatch('swal:success', message: __('app.match_ended'));
    }

    // ── Score control ──────────────────────────────────────────────

    public function scoreUp($team)
    {
        if ($team === 1) {
            $this->score1++;
        } else {
            $this->score2++;
        }
        $this->persistScore();
    }

    public function scoreDown($team)
    {
        if ($team === 1 && $this->score1 > 0) {
            $this->score1--;
        } elseif ($team === 2 && $this->score2 > 0) {
            $this->score2--;
        }
        $this->persistScore();
    }

    protected function persistScore()
    {
        app(MatchService::class)->updateScore($this->match, $this->score1, $this->score2);
        $this->dispatch('swal:success', message: __('app.score_updated'));
    }

    // ── Added time ─────────────────────────────────────────────────

    public function saveAddedTime()
    {
        app(MatchService::class)->saveAddedTime(
            $this->match,
            $this->addedTime1,
            $this->addedTime2,
            $this->addedTimeET1,
            $this->addedTimeET2,
        );

        $this->dispatch('swal:success', message: __('app.added_time_saved'));
    }

    // ── Quick events ──────────────────────────────────────────────

    private function resetEventForm(): void
    {
        $this->eventDescription = '';
        $this->selectedPlayerId = null;
        $this->eventMinute = '';
    }

    private function reloadEvents(): void
    {
        $this->match->load('events');
    }

    public function quickGoal($teamId)
    {
        app(MatchService::class)->handleGoal($this->match, $teamId, __('app.goal'));

        $this->score1 = $this->match->score_team1;
        $this->score2 = $this->match->score_team2;
        $this->resetEventForm();
        $this->reloadEvents();
        $this->dispatch('swal:success', message: __('app.event_added'));
    }

    public function quickYellowCard($teamId)
    {
        app(MatchService::class)->addEvent($this->match, $teamId, 'yellow_card', __('app.yellow_card'));
        $this->resetEventForm();
        $this->reloadEvents();
        $this->dispatch('swal:success', message: __('app.event_added'));
    }

    public function quickRedCard($teamId)
    {
        app(MatchService::class)->addEvent($this->match, $teamId, 'red_card', __('app.red_card'));
        $this->resetEventForm();
        $this->reloadEvents();
        $this->dispatch('swal:success', message: __('app.event_added'));
    }

    public function quickSubstitution($teamId)
    {
        if (! $this->selectedPlayerId) {
            $this->dispatch('swal:error', message: __('app.select_player_first'));

            return;
        }

        $playerTeamId = Player::where('id', $this->selectedPlayerId)->value('team_id');
        if ($playerTeamId !== $teamId) {
            $this->dispatch('swal:error', message: __('app.player_not_in_team'));

            return;
        }

        app(MatchService::class)->handleSubstitution($this->match, $teamId, $this->selectedPlayerId);
        $this->resetEventForm();
        $this->reloadEvents();
        $this->dispatch('swal:success', message: __('app.event_added'));
    }

    public function quickOwnGoal($teamId)
    {
        app(MatchService::class)->handleOwnGoal($this->match, $teamId, __('app.own_goal'));

        $this->score1 = $this->match->score_team1;
        $this->score2 = $this->match->score_team2;
        $this->resetEventForm();
        $this->reloadEvents();
        $this->dispatch('swal:success', message: __('app.event_added'));
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function updatedSelectedPlayerId($value): void
    {
        if (! $value || $this->eventDescription) {
            return;
        }
        $player = $this->team1Players->firstWhere('id', $value)
            ?? $this->team2Players->firstWhere('id', $value);
        if ($player) {
            $this->eventDescription = $player->name;
        }
    }

    protected function refresh()
    {
        $this->match->refresh();
        $this->score1 = $this->match->score_team1 ?? 0;
        $this->score2 = $this->match->score_team2 ?? 0;
        $this->addedTime1 = $this->match->added_time_first_half ?? 0;
        $this->addedTime2 = $this->match->added_time_second_half ?? 0;
        $this->addedTimeET1 = $this->match->extra_data['added_time_et_first_half'] ?? 0;
        $this->addedTimeET2 = $this->match->extra_data['added_time_et_second_half'] ?? 0;
        $this->supportsET = $this->match->competition?->format_config['extra_time'] ?? false;
        $this->loadPlayers();
    }

    public function render()
    {
        $playersByTeam = [
            $this->match->team1_id => $this->team1Players,
            $this->match->team2_id => $this->team2Players,
        ];

        return view('livewire.admin.matches.match-control-page', [
            'title' => __('app.match_control').' — '.($this->match->team1->name ?? '?').' vs '.($this->match->team2->name ?? '?'),
            'playersByTeam' => $playersByTeam,
            'supportsET' => $this->supportsET,
        ]);
    }
}
