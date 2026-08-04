<?php

namespace App\Listeners;

use App\Events\GoalScored;
use App\Events\MatchCompleted;
use App\Events\MatchScheduled;
use App\Events\MatchStarted;
use App\Models\Match_;
use App\Models\Registration;
use App\Models\Team;
use App\Models\TeamStaff;
use App\Services\NotificationService;
use Illuminate\Support\Collection;

class NotifyMatchParticipants
{
    public function __construct(private NotificationService $notifier) {}

    public function handleMatchScheduled(MatchScheduled $event): void
    {
        $match = $event->match;
        $competition = $match->competition;

        if (! $competition) {
            return;
        }

        $this->notifyParticipants(
            $match,
            __('app.match_scheduled_title'),
            __('app.match_scheduled_notification', [
                'match' => $this->matchLabel($match),
                'competition' => $competition->name,
            ]),
            'bi-calendar-event-fill text-info',
            route('matches.index'),
            'info',
        );
    }

    public function handleMatchStarted(MatchStarted $event): void
    {
        $match = $event->match;

        $this->notifyParticipants(
            $match,
            __('app.match_started_title'),
            __('app.match_started_notification', ['match' => $this->matchLabel($match)]),
            'bi-play-circle-fill text-success',
            route('matches.live', ['match' => $match]),
            'success',
        );
    }

    public function handleMatchCompleted(MatchCompleted $event): void
    {
        $match = $event->match;
        $competition = $match->competition;

        if (! $competition) {
            return;
        }

        $this->notifyParticipants(
            $match,
            __('app.match_completed'),
            __('app.match_completed_notification', [
                'match' => $this->matchLabel($match),
                'competition' => $competition->name,
            ]),
            'bi-trophy-fill text-warning',
            route('competitions.show', ['competition' => $competition]),
            'success',
        );
    }

    public function handleGoalScored(GoalScored $event): void
    {
        $matchEvent = $event->event;
        $match = $matchEvent->match;

        if (! $match || ! $match->competition) {
            return;
        }

        $player = $matchEvent->player;
        $team = $matchEvent->team;

        $this->notifyParticipants(
            $match,
            __('app.goal_scored'),
            __('app.goal_scored_notification', [
                'player' => $player?->user?->name ?? $matchEvent->player_id,
                'team' => $team?->name ?? '',
                'minute' => $matchEvent->minute,
            ]),
            'bi-circle-fill text-success',
            route('matches.live', ['match' => $match]),
            'success',
        );
    }

    protected function matchLabel(Match_ $match): string
    {
        $team1 = $match->team1;
        $team2 = $match->team2;

        if (! $team1 || ! $team2) {
            return "{$match->team1_id}-{$match->team2_id}";
        }

        return "{$team1->name} vs {$team2->name}";
    }

    protected function notifyParticipants(Match_ $match, string $title, string $message, string $icon, string $link, string $type): void
    {
        foreach ($this->recipientsForMatch($match) as $user) {
            $this->notifier->notifyUser($user, $title, $message, $icon, $link, $type);
        }
    }

    protected function recipientsForMatch(Match_ $match): Collection
    {
        $users = collect();
        $teamIds = array_filter([$match->team1_id, $match->team2_id]);

        if ($teamIds) {
            foreach (Team::whereIn('id', $teamIds)->get() as $team) {
                if ($team->captain) {
                    $users->push($team->captain);
                }
            }

            $staff = TeamStaff::whereIn('team_id', $teamIds)
                ->where('is_active', true)
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            $users = $users->merge($staff);
        }

        if ($match->competition_id) {
            $registrants = Registration::where('competition_id', $match->competition_id)
                ->where('participant_type', Registration::PARTICIPANT_INDIVIDUAL)
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            $users = $users->merge($registrants);
        }

        return $users->filter()->unique('id')->values();
    }
}
