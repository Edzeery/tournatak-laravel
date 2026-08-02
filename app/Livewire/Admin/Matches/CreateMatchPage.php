<?php

namespace App\Livewire\Admin\Matches;

use App\Livewire\Concerns\Notifies;
use App\Models\Competition;
use App\Models\Referee;
use App\Models\Team;
use App\Services\MatchService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateMatchPage extends Component
{
    use Notifies;

    public ?int $competition_id = null;

    public ?int $team1_id = null;

    public ?int $team2_id = null;

    public ?string $match_date = null;

    public string $status = 'upcoming';

    public ?int $referee_id = null;

    public ?int $assistant_referee_1_id = null;

    public ?int $assistant_referee_2_id = null;

    public ?int $fourth_official_id = null;

    public function store()
    {
        $this->validate([
            'competition_id' => 'required|exists:competitions,id',
            'team1_id' => 'required|exists:teams,id',
            'team2_id' => 'required|exists:teams,id',
            'match_date' => 'nullable|date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed,abandoned,pending',
            'referee_id' => 'nullable|exists:referees,id',
            'assistant_referee_1_id' => 'nullable|exists:referees,id',
            'assistant_referee_2_id' => 'nullable|exists:referees,id',
            'fourth_official_id' => 'nullable|exists:referees,id',
        ]);

        if ($this->team1_id === $this->team2_id) {
            $this->notify('error', __('app.teams_must_be_different'));

            return;
        }

        app(MatchService::class)->create([
            'competition_id' => $this->competition_id,
            'team1_id' => $this->team1_id,
            'team2_id' => $this->team2_id,
            'match_date' => $this->match_date,
            'status' => $this->status,
            'referee_id' => $this->referee_id,
            'assistant_referee_1_id' => $this->assistant_referee_1_id,
            'assistant_referee_2_id' => $this->assistant_referee_2_id,
            'fourth_official_id' => $this->fourth_official_id,
        ]);

        session()->flash('success', __('app.match_created'));

        return redirect()->route('admin.matches.index');
    }

    public function render()
    {
        return view('livewire.admin.matches.create-match-page', [
            'title' => __('app.add_new_match'),
            'competitions' => Competition::orderBy('name')->get(),
            'teams' => Team::orderBy('name')->get(),
            'referees' => Referee::active()->orderBy('name')->get(),
        ]);
    }
}
