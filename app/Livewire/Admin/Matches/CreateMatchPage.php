<?php
namespace App\Livewire\Admin\Matches;

use App\Models\Match_;
use App\Models\Competition;
use App\Models\Team;
use App\Services\MatchService;
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
        $service = app(MatchService::class);
        $this->validate($service->getValidationRules());

        $service->validateSameTeams($this->team1_id, $this->team2_id);

        $service->create([
            'competition_id' => $this->competition_id,
            'team1_id' => $this->team1_id,
            'team2_id' => $this->team2_id,
            'match_date' => $this->match_date,
            'status' => $this->status,
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
        ]);
    }
}
