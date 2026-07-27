<?php

namespace App\Livewire\Admin\Matches;

use App\Models\Match_;
use App\Models\MatchEvent;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class MatchEventsPage extends Component
{
    public $matchId;
    public $match;
    public $events = [];
    public bool $showModal = false;
    public ?int $editingEventId = null;

    public $eventForm = [
        'team_id' => null,
        'player_id' => null,
        'event_type' => null,
        'minute' => null,
        'added_time' => 0,
        'description' => null,
        'related_player_id' => null,
    ];

    public function mount(Match_ $match): void
    {
        $this->matchId = $match->id;
        $this->match = $match->load(['team1', 'team2']);
        $this->loadEvents();
    }

    public function loadEvents()
    {
        $this->events = MatchEvent::with(['player.user', 'relatedPlayer.user'])
            ->where('match_id', $this->matchId)
            ->orderBy('minute')
            ->orderBy('added_time')
            ->get();
    }

    public function updatedEventFormTeamId()
    {
        $this->eventForm['player_id'] = null;
        $this->eventForm['related_player_id'] = null;
    }

    public function saveEvent()
    {
        $this->validate([
            'eventForm.team_id' => 'required|exists:teams,id',
            'eventForm.player_id' => 'required|exists:players,id',
            'eventForm.event_type' => 'required|in:' . implode(',', array_keys(MatchEvent::EVENT_TYPES)),
            'eventForm.minute' => 'required|integer|min:0|max:120',
            'eventForm.added_time' => 'nullable|integer|min:0|max:15',
            'eventForm.description' => 'nullable|string|max:255',
            'eventForm.related_player_id' => 'nullable|exists:players,id',
        ]);

        if (!in_array($this->eventForm['team_id'], [$this->match->team1_id, $this->match->team2_id])) {
            session()->flash('error', __('app.invalid_team'));
            return;
        }

        if ($this->editingEventId) {
            $event = MatchEvent::findOrFail($this->editingEventId);
            $event->update([
                'team_id' => $this->eventForm['team_id'],
                'player_id' => $this->eventForm['player_id'],
                'event_type' => $this->eventForm['event_type'],
                'minute' => $this->eventForm['minute'],
                'added_time' => $this->eventForm['added_time'] ?? 0,
                'description' => $this->eventForm['description'],
                'related_player_id' => $this->eventForm['related_player_id'],
            ]);
            session()->flash('success', __('app.event_updated'));
        } else {
            MatchEvent::create([
                'match_id' => $this->matchId,
                'team_id' => $this->eventForm['team_id'],
                'player_id' => $this->eventForm['player_id'],
                'event_type' => $this->eventForm['event_type'],
                'minute' => $this->eventForm['minute'],
                'added_time' => $this->eventForm['added_time'] ?? 0,
                'description' => $this->eventForm['description'],
                'related_player_id' => $this->eventForm['related_player_id'],
            ]);
            session()->flash('success', __('app.event_added'));
        }

        $this->closeModal();
        $this->loadEvents();
    }

    public function editEvent($id)
    {
        $event = MatchEvent::findOrFail($id);
        $this->editingEventId = $id;
        $this->eventForm = [
            'team_id' => $event->team_id,
            'player_id' => $event->player_id,
            'event_type' => $event->event_type,
            'minute' => $event->minute,
            'added_time' => $event->added_time,
            'description' => $event->description,
            'related_player_id' => $event->related_player_id,
        ];
        $this->showModal = true;
    }

    public function deleteEvent($id)
    {
        MatchEvent::findOrFail($id)->delete();
        session()->flash('success', __('app.event_deleted'));
        $this->loadEvents();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingEventId = null;
        $this->eventForm = [
            'team_id' => null,
            'player_id' => null,
            'event_type' => null,
            'minute' => null,
            'added_time' => 0,
            'description' => null,
            'related_player_id' => null,
        ];
    }

    public function render()
    {
        $team1Players = Player::with('user')->where('team_id', $this->match->team1_id)->orderBy('number')->get();
        $team2Players = Player::with('user')->where('team_id', $this->match->team2_id)->orderBy('number')->get();

        $activeTeamPlayers = $this->eventForm['team_id'] == $this->match->team1_id
            ? $team1Players
            : ($this->eventForm['team_id'] == $this->match->team2_id ? $team2Players : collect());

        return view('livewire.admin.matches.events-page', [
            'title' => __('app.page_title_match_events') . ' - ' . $this->match->team1->name . ' vs ' . $this->match->team2->name,
            'match' => $this->match,
            'events' => $this->events,
            'team1Players' => $team1Players,
            'team2Players' => $team2Players,
            'activeTeamPlayers' => $activeTeamPlayers,
            'eventTypes' => MatchEvent::EVENT_TYPES,
        ]);
    }
}
