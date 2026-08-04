<?php

namespace App\Services;

use App\Enums\MatchStatus;
use App\Events\GoalScored;
use App\Events\MatchCompleted;
use App\Events\MatchScheduled;
use App\Events\MatchStarted;
use App\Models\Match_;
use App\Models\MatchEvent;
use App\Models\MatchLineup;
use Illuminate\Support\Collection;

class MatchService
{
    public function create(array $data): Match_
    {
        $match = Match_::create($data);

        event(new MatchScheduled($match->fresh()));

        return $match;
    }

    public function transitionPhase(Match_ $match, string $phase): void
    {
        $extra = $match->extra_data ?? [];
        $extra['phase'] = $phase;

        $updates = ['extra_data' => $extra];

        if ($phase === Match_::PHASE_FIRST_HALF) {
            $extra['first_half_started_at'] = now()->toIso8601String();
            $updates = [
                'status' => MatchStatus::InProgress->value,
                'score_team1' => 0,
                'score_team2' => 0,
                'match_date' => $match->match_date ?? now(),
                'extra_data' => $extra,
            ];
        } elseif ($phase === Match_::PHASE_SECOND_HALF) {
            $extra['second_half_started_at'] = now()->toIso8601String();
            $updates['extra_data'] = $extra;
        } elseif ($phase === Match_::PHASE_ET_FIRST_HALF) {
            $extra['et_first_half_started_at'] = now()->toIso8601String();
            $updates['extra_data'] = $extra;
        } elseif ($phase === Match_::PHASE_ET_SECOND_HALF) {
            $extra['et_second_half_started_at'] = now()->toIso8601String();
            $updates['extra_data'] = $extra;
        } elseif (in_array($phase, [Match_::PHASE_FULL_TIME, Match_::PHASE_ET_BREAK])) {
            if ($phase === Match_::PHASE_FULL_TIME) {
                $updates['status'] = MatchStatus::Completed->value;
            }
        }

        $match->update($updates);

        if ($phase === Match_::PHASE_FIRST_HALF) {
            event(new MatchStarted($match->fresh()));
        } elseif ($phase === Match_::PHASE_FULL_TIME) {
            event(new MatchCompleted($match->fresh()));
        }
    }

    public function updateScore(Match_ $match, int $scoreTeam1, int $scoreTeam2): void
    {
        $match->update([
            'score_team1' => $scoreTeam1,
            'score_team2' => $scoreTeam2,
        ]);
    }

    public function addEvent(Match_ $match, int $teamId, string $eventType, string $description, ?int $playerId = null, ?int $minute = null): MatchEvent
    {
        if ($minute === null) {
            $minute = $this->computeCurrentMinute($match);
        }

        return MatchEvent::create([
            'match_id' => $match->id,
            'team_id' => $teamId,
            'player_id' => $playerId,
            'event_type' => $eventType,
            'minute' => $minute,
            'description' => $description,
        ]);
    }

    public function handleGoal(Match_ $match, int $teamId, string $description): MatchEvent
    {
        $event = $this->addEvent($match, $teamId, 'goal', $description);

        if ($teamId === $match->team1_id) {
            $match->increment('score_team1');
        } else {
            $match->increment('score_team2');
        }

        event(new GoalScored($event));

        return $event;
    }

    public function handleOwnGoal(Match_ $match, int $teamId, string $description): MatchEvent
    {
        $event = $this->addEvent($match, $teamId, 'own_goal', $description);

        if ($teamId === $match->team1_id) {
            $match->increment('score_team2');
        } else {
            $match->increment('score_team1');
        }

        return $event;
    }

    public function handleSubstitution(Match_ $match, int $teamId, int $playerId): MatchEvent
    {
        $minute = $this->computeCurrentMinute($match);
        $lineup = MatchLineup::where('match_id', $match->id)
            ->where('team_id', $teamId)
            ->where('player_id', $playerId)
            ->first();

        if ($lineup && $lineup->is_starter && ! $lineup->minute_out) {
            $lineup->update(['minute_out' => $minute]);
            $eventType = 'substitution_out';
        } else {
            MatchLineup::where('match_id', $match->id)
                ->where('team_id', $teamId)
                ->where('player_id', $playerId)
                ->update(['minute_in' => $minute]);
            $eventType = 'substitution_in';
        }

        return $this->addEvent($match, $teamId, $eventType, __('app.substitution'), $playerId, $minute);
    }

    public function saveAddedTime(Match_ $match, int $addedTime1, int $addedTime2, int $addedTimeET1 = 0, int $addedTimeET2 = 0): void
    {
        $extra = $match->extra_data ?? [];
        $extra['added_time_et_first_half'] = $addedTimeET1;
        $extra['added_time_et_second_half'] = $addedTimeET2;

        $match->update([
            'added_time_first_half' => $addedTime1,
            'added_time_second_half' => $addedTime2,
            'extra_data' => $extra,
        ]);
    }

    public function computeCurrentMinute(Match_ $match, ?int $overrideMinute = null): int
    {
        if ($overrideMinute !== null) {
            return max(0, $overrideMinute);
        }

        $extra = $match->extra_data ?? [];
        $phase = $extra['phase'] ?? Match_::PHASE_SCHEDULED;
        $now = now()->timestamp;

        $phaseStarts = [
            Match_::PHASE_FIRST_HALF => ['key' => 'first_half_started_at', 'offset' => 0],
            Match_::PHASE_SECOND_HALF => ['key' => 'second_half_started_at', 'offset' => 45],
            Match_::PHASE_ET_FIRST_HALF => ['key' => 'et_first_half_started_at', 'offset' => 90],
            Match_::PHASE_ET_SECOND_HALF => ['key' => 'et_second_half_started_at', 'offset' => 105],
        ];

        if (isset($phaseStarts[$phase])) {
            $start = $extra[$phaseStarts[$phase]['key']] ?? null;
            if ($start) {
                return $phaseStarts[$phase]['offset'] + max(1, (int) (($now - strtotime($start)) / 60));
            }
        }

        return 0;
    }

    public function getLineupPlayers(Match_ $match, int $teamId): Collection
    {
        return MatchLineup::with('player')
            ->where('match_id', $match->id)
            ->where('team_id', $teamId)
            ->get()
            ->pluck('player');
    }
}
