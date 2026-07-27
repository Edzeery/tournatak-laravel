<?php
namespace App\Livewire\Admin\Matches;

use App\Models\Match_;
use App\Models\Competition;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateMatchPage extends Component
{
    public ?int $competition_id = null;
    public ?int $team1_id = null;
    public ?int $team2_id = null;
    public ?string $match_date = null;
    public string $status = 'upcoming';

    public function store()
    {
        $this->validate([
            'competition_id' => 'required|exists:competitions,id',
            'team1_id' => 'required|exists:teams,id',
            'team2_id' => 'required|exists:teams,id',
            'match_date' => 'nullable|date',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);

        if ($this->team1_id === $this->team2_id) {
            session()->flash('error', 'يجب أن يكون الفريقان مختلفين');
            return;
        }

        Match_::create([
            'competition_id' => $this->competition_id,
            'team1_id' => $this->team1_id,
            'team2_id' => $this->team2_id,
            'match_date' => $this->match_date,
            'status' => $this->status,
            'score_team1' => 0,
            'score_team2' => 0,
        ]);

        session()->flash('success', 'تم إنشاء المباراة بنجاح');
        return redirect()->route('admin.matches.index');
    }

    public function render()
    {
        return view('livewire.admin.matches.create-match-page', [
            'title' => 'إضافة مباراة',
            'competitions' => Competition::orderBy('name')->get(),
            'teams' => Team::orderBy('name')->get(),
        ]);
    }
}
