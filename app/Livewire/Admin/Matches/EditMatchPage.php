<?php
namespace App\Livewire\Admin\Matches;

use App\Models\Match_;
use App\Models\Competition;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class EditMatchPage extends Component
{
    public Match_ $match;
    public ?int $competition_id = null;
    public ?int $team1_id = null;
    public ?int $team2_id = null;
    public ?string $match_date = null;
    public string $status = 'upcoming';
    public int $score_team1 = 0;
    public int $score_team2 = 0;

    public function mount(Match_ $match)
    {
        $this->authorize('update', $match);

        $this->match = $match;
        $this->competition_id = $match->competition_id;
        $this->team1_id = $match->team1_id;
        $this->team2_id = $match->team2_id;
        $this->match_date = $match->match_date?->format('Y-m-d\TH:i');
        $this->status = $match->status;
        $this->score_team1 = $match->score_team1 ?? 0;
        $this->score_team2 = $match->score_team2 ?? 0;
    }

    public function update()
    {
        $this->validate([
            'competition_id' => 'required|exists:competitions,id',
            'team1_id' => 'required|exists:teams,id',
            'team2_id' => 'required|exists:teams,id',
            'match_date' => 'nullable|date',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'score_team1' => 'integer|min:0',
            'score_team2' => 'integer|min:0',
        ]);

        if ($this->team1_id === $this->team2_id) {
            session()->flash('error', __('app.teams_must_be_different'));
            return;
        }

        $this->match->update([
            'competition_id' => $this->competition_id,
            'team1_id' => $this->team1_id,
            'team2_id' => $this->team2_id,
            'match_date' => $this->match_date,
            'status' => $this->status,
            'score_team1' => $this->score_team1,
            'score_team2' => $this->score_team2,
        ]);

        session()->flash('success', __('app.match_updated'));
        return redirect()->route('admin.matches.index');
    }

    public function render()
    {
        return view('livewire.admin.matches.edit-match-page', [
            'title' => __('app.edit_match'),
            'match' => $this->match,
            'competitions' => Competition::orderBy('name')->get(),
            'teams' => Team::orderBy('name')->get(),
        ]);
    }
}
