<?php

namespace App\Livewire\Admin\Matches;

use App\Models\Match_;
use App\Models\MatchEvent;
use App\Models\MatchLineup;
use App\Models\Player;
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
            'events' => fn($q) => $q->with('player', 'team')->latest('minute'),
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
        $this->team1Players = MatchLineup::with('player')
            ->where('match_id', $this->match->id)
            ->where('team_id', $this->match->team1_id)
            ->get()
            ->pluck('player');

        $this->team2Players = MatchLineup::with('player')
            ->where('match_id', $this->match->id)
            ->where('team_id', $this->match->team2_id)
            ->get()
            ->pluck('player');
    }

    // ── Phase transitions ──────────────────────────────────────────

    public function startFirstHalf()
    {
        $extra = $this->match->extra_data ?? [];
        $extra['phase'] = Match_::PHASE_FIRST_HALF;
        $extra['first_half_started_at'] = now()->toIso8601String();

        $this->match->update([
            'status' => Match_::STATUS_IN_PROGRESS,
            'score_team1' => 0,
            'score_team2' => 0,
            'match_date' => $this->match->match_date ?? now(),
            'extra_data' => $extra,
        ]);

        $this->refresh();
        $this->dispatch('swal:success', message: __('app.first_half_started'));
    }

    public function endFirstHalf()
    {
        $extra = $this->match->extra_data ?? [];
        $extra['phase'] = Match_::PHASE_HALF_TIME;
        $this->match->update(['extra_data' => $extra]);
        $this->refresh();
        $this->dispatch('swal:info', message: __('app.first_half_ended'));
    }

    public function startSecondHalf()
    {
        $extra = $this->match->extra_data ?? [];
        $extra['phase'] = Match_::PHASE_SECOND_HALF;
        $extra['second_half_started_at'] = now()->toIso8601String();
        $this->match->update(['extra_data' => $extra]);
        $this->refresh();
        $this->dispatch('swal:success', message: __('app.second_half_started'));
    }

    public function endSecondHalf()
    {
        $extra = $this->match->extra_data ?? [];

        if ($this->supportsET) {
            $extra['phase'] = Match_::PHASE_ET_BREAK;
            $this->match->update(['extra_data' => $extra]);
            $this->dispatch('swal:info', message: __('app.second_half_ended'));
        } else {
            $extra['phase'] = Match_::PHASE_FULL_TIME;
            $this->match->update([
                'status' => Match_::STATUS_COMPLETED,
                'extra_data' => $extra,
            ]);
            $this->dispatch('swal:success', message: __('app.match_ended'));
        }

        $this->refresh();
    }

    public function startETFirstHalf()
    {
        $extra = $this->match->extra_data ?? [];
        $extra['phase'] = Match_::PHASE_ET_FIRST_HALF;
        $extra['et_first_half_started_at'] = now()->toIso8601String();
        $this->match->update(['extra_data' => $extra]);
        $this->refresh();
        $this->dispatch('swal:success', message: __('app.et_first_half_started'));
    }

    public function endETFirstHalf()
    {
        $extra = $this->match->extra_data ?? [];
        $extra['phase'] = Match_::PHASE_ET_HALF_TIME;
        $this->match->update(['extra_data' => $extra]);
        $this->refresh();
        $this->dispatch('swal:info', message: __('app.et_first_half_ended'));
    }

    public function startETSecondHalf()
    {
        $extra = $this->match->extra_data ?? [];
        $extra['phase'] = Match_::PHASE_ET_SECOND_HALF;
        $extra['et_second_half_started_at'] = now()->toIso8601String();
        $this->match->update(['extra_data' => $extra]);
        $this->refresh();
        $this->dispatch('swal:success', message: __('app.et_second_half_started'));
    }

    public function endMatch()
    {
        $extra = $this->match->extra_data ?? [];
        $extra['phase'] = Match_::PHASE_FULL_TIME;
        $this->match->update([
            'status' => Match_::STATUS_COMPLETED,
            'extra_data' => $extra,
        ]);
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
        $this->match->update([
            'score_team1' => $this->score1,
            'score_team2' => $this->score2,
        ]);
        $this->dispatch('swal:success', message: __('app.score_updated'));
    }

    // ── Added time ─────────────────────────────────────────────────

    public function saveAddedTime()
    {
        $extra = $this->match->extra_data ?? [];
        $extra['added_time_et_first_half'] = $this->addedTimeET1;
        $extra['added_time_et_second_half'] = $this->addedTimeET2;

        $this->match->update([
            'added_time_first_half' => $this->addedTime1,
            'added_time_second_half' => $this->addedTime2,
            'extra_data' => $extra,
        ]);

        $this->dispatch('swal:success', message: __('app.added_time_saved'));
    }

    // ── Quick events ──────────────────────────────────────────────

    private function buildEventData($teamId, $eventType, $description): array
    {
        return [
            'match_id' => $this->match->id,
            'team_id' => $teamId,
            'player_id' => $this->selectedPlayerId,
            'event_type' => $eventType,
            'minute' => $this->computeCurrentMinute(),
            'description' => $this->eventDescription ?: $description,
        ];
    }

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
        MatchEvent::create($this->buildEventData($teamId, 'goal', __('app.goal')));

        if ($teamId === $this->match->team1_id) {
            $this->score1++;
        } else {
            $this->score2++;
        }
        $this->persistScore();

        $this->resetEventForm();
        $this->reloadEvents();
        $this->dispatch('swal:success', message: __('app.event_added'));
    }

    public function quickYellowCard($teamId)
    {
        MatchEvent::create($this->buildEventData($teamId, 'yellow_card', __('app.yellow_card')));
        $this->resetEventForm();
        $this->reloadEvents();
        $this->dispatch('swal:success', message: __('app.event_added'));
    }

    public function quickRedCard($teamId)
    {
        MatchEvent::create($this->buildEventData($teamId, 'red_card', __('app.red_card')));
        $this->resetEventForm();
        $this->reloadEvents();
        $this->dispatch('swal:success', message: __('app.event_added'));
    }

    public function quickSubstitution($teamId)
    {
        if (!$this->selectedPlayerId) {
            $this->dispatch('swal:error', message: __('app.select_player_first'));
            return;
        }

        $playerTeamId = Player::where('id', $this->selectedPlayerId)->value('team_id');
        if ($playerTeamId !== $teamId) {
            $this->dispatch('swal:error', message: __('app.player_not_in_team'));
            return;
        }

        $minute = $this->computeCurrentMinute();
        $lineup = MatchLineup::where('match_id', $this->match->id)
            ->where('team_id', $teamId)
            ->where('player_id', $this->selectedPlayerId)
            ->first();

        if ($lineup && $lineup->is_starter && !$lineup->minute_out) {
            $lineup->update(['minute_out' => $minute]);
            $eventType = 'substitution_out';
        } else {
            MatchLineup::where('match_id', $this->match->id)
                ->where('team_id', $teamId)
                ->where('player_id', $this->selectedPlayerId)
                ->update(['minute_in' => $minute]);
            $eventType = 'substitution_in';
        }

        MatchEvent::create($this->buildEventData($teamId, $eventType, __('app.substitution')));

        $this->resetEventForm();
        $this->reloadEvents();
        $this->dispatch('swal:success', message: __('app.event_added'));
    }

    public function quickOwnGoal($teamId)
    {
        MatchEvent::create($this->buildEventData($teamId, 'own_goal', __('app.own_goal')));

        if ($teamId === $this->match->team1_id) {
            $this->score2++;
        } else {
            $this->score1++;
        }
        $this->persistScore();

        $this->resetEventForm();
        $this->reloadEvents();
        $this->dispatch('swal:success', message: __('app.event_added'));
    }

    protected function computeCurrentMinute(): int
    {
        if ($this->eventMinute !== '') {
            return max(0, (int)$this->eventMinute);
        }

        $extra = $this->match->extra_data ?? [];
        $phase = $extra['phase'] ?? Match_::PHASE_SCHEDULED;
        $now = now()->timestamp;

        if ($phase === Match_::PHASE_FIRST_HALF) {
            $start = $extra['first_half_started_at'] ?? null;
            if ($start) return max(1, (int)(($now - strtotime($start)) / 60));
        }
        if ($phase === Match_::PHASE_SECOND_HALF) {
            $start = $extra['second_half_started_at'] ?? null;
            if ($start) return 45 + max(1, (int)(($now - strtotime($start)) / 60));
        }
        if ($phase === Match_::PHASE_ET_FIRST_HALF) {
            $start = $extra['et_first_half_started_at'] ?? null;
            if ($start) return 90 + max(1, (int)(($now - strtotime($start)) / 60));
        }
        if ($phase === Match_::PHASE_ET_SECOND_HALF) {
            $start = $extra['et_second_half_started_at'] ?? null;
            if ($start) return 105 + max(1, (int)(($now - strtotime($start)) / 60));
        }

        return 0;
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function updatedSelectedPlayerId($value): void
    {
        if (!$value || $this->eventDescription) return;
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
            'title' => __('app.match_control') . ' — ' . ($this->match->team1->name ?? '?') . ' vs ' . ($this->match->team2->name ?? '?'),
            'playersByTeam' => $playersByTeam,
            'supportsET' => $this->supportsET,
        ]);
    }
}
