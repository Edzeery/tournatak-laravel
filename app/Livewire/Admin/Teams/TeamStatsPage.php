<?php

namespace App\Livewire\Admin\Teams;

use App\Models\Competition;
use App\Models\Team;
use App\Models\TeamSeasonStat;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class TeamStatsPage extends Component
{
    public $teamId;
    public $team;
    public $seasonStats = [];
    public $selectedSeason = '';
    public $availableSeasons = [];

    public $showModal = false;
    public $editingStatId = null;

    public $statForm = [
        'competition_id' => '',
        'season_year' => '',
        'matches_played' => 0,
        'wins' => 0,
        'draws' => 0,
        'losses' => 0,
        'goals_for' => 0,
        'goals_against' => 0,
        'clean_sheets' => 0,
        'points' => 0,
        'yellow_cards' => 0,
        'red_cards' => 0,
        'possession_avg' => 0,
        'shots_per_match' => 0,
    ];

    public $availableCompetitions = [];

    public function mount(Team $team): void
    {
        $this->authorize('update', $team);

        $this->teamId = $team->id;
        $this->team = $team;

        $this->availableCompetitions = Competition::orderBy('name')->get();

        $this->availableSeasons = TeamSeasonStat::where('team_id', $this->teamId)
            ->distinct()
            ->pluck('season_year')
            ->sortDesc()
            ->values();

        if ($this->availableSeasons->isNotEmpty() && !$this->selectedSeason) {
            $this->selectedSeason = $this->availableSeasons->first();
        }

        $this->loadStats();
    }

    public function updatedSelectedSeason(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $query = TeamSeasonStat::with('competition')
            ->where('team_id', $this->teamId);

        if ($this->selectedSeason) {
            $query->where('season_year', $this->selectedSeason);
        }

        $this->seasonStats = $query->latest('season_year')->get();
    }

    public function openModal(): void
    {
        $this->editingStatId = null;
        $this->statForm = [
            'competition_id' => '',
            'season_year' => now()->year,
            'matches_played' => 0,
            'wins' => 0,
            'draws' => 0,
            'losses' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'clean_sheets' => 0,
            'points' => 0,
            'yellow_cards' => 0,
            'red_cards' => 0,
            'possession_avg' => 0,
            'shots_per_match' => 0,
        ];
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingStatId = null;
        $this->resetValidation();
    }

    public function editStat($id): void
    {
        $stat = TeamSeasonStat::findOrFail($id);

        $this->editingStatId = $id;
        $this->statForm = [
            'competition_id' => $stat->competition_id,
            'season_year' => $stat->season_year,
            'matches_played' => $stat->matches_played,
            'wins' => $stat->wins,
            'draws' => $stat->draws,
            'losses' => $stat->losses,
            'goals_for' => $stat->goals_for,
            'goals_against' => $stat->goals_against,
            'clean_sheets' => $stat->clean_sheets,
            'points' => $stat->points,
            'yellow_cards' => $stat->yellow_cards,
            'red_cards' => $stat->red_cards,
            'possession_avg' => $stat->possession_avg,
            'shots_per_match' => $stat->shots_per_match,
        ];
        $this->showModal = true;
    }

    public function saveStat(): void
    {
        $this->validate([
            'statForm.competition_id' => 'required|exists:competitions,id',
            'statForm.season_year' => 'required|integer|min:2000|max:2100',
            'statForm.matches_played' => 'required|integer|min:0',
            'statForm.wins' => 'required|integer|min:0',
            'statForm.draws' => 'required|integer|min:0',
            'statForm.losses' => 'required|integer|min:0',
            'statForm.goals_for' => 'required|integer|min:0',
            'statForm.goals_against' => 'required|integer|min:0',
            'statForm.clean_sheets' => 'required|integer|min:0',
            'statForm.points' => 'required|integer|min:0',
            'statForm.yellow_cards' => 'required|integer|min:0',
            'statForm.red_cards' => 'required|integer|min:0',
            'statForm.possession_avg' => 'required|numeric|min:0|max:100',
            'statForm.shots_per_match' => 'required|numeric|min:0',
        ]);

        $data = [
            'team_id' => $this->teamId,
            'competition_id' => $this->statForm['competition_id'],
            'season_year' => $this->statForm['season_year'],
            'matches_played' => $this->statForm['matches_played'],
            'wins' => $this->statForm['wins'],
            'draws' => $this->statForm['draws'],
            'losses' => $this->statForm['losses'],
            'goals_for' => $this->statForm['goals_for'],
            'goals_against' => $this->statForm['goals_against'],
            'clean_sheets' => $this->statForm['clean_sheets'],
            'points' => $this->statForm['points'],
            'yellow_cards' => $this->statForm['yellow_cards'],
            'red_cards' => $this->statForm['red_cards'],
            'possession_avg' => $this->statForm['possession_avg'],
            'shots_per_match' => $this->statForm['shots_per_match'],
        ];

        if ($this->editingStatId) {
            TeamSeasonStat::where('id', $this->editingStatId)
                ->where('team_id', $this->teamId)
                ->update($data);
            session()->flash('success', __('app.team_stat_saved'));
        } else {
            TeamSeasonStat::create($data);
            session()->flash('success', __('app.team_stat_saved'));
        }

        $this->closeModal();

        $this->availableSeasons = TeamSeasonStat::where('team_id', $this->teamId)
            ->distinct()
            ->pluck('season_year')
            ->sortDesc()
            ->values();

        if ($this->availableSeasons->contains($this->statForm['season_year'])) {
            $this->selectedSeason = $this->statForm['season_year'];
        }

        $this->loadStats();
    }

    public function deleteStat($id): void
    {
        TeamSeasonStat::where('id', $id)
            ->where('team_id', $this->teamId)
            ->delete();

        $this->availableSeasons = TeamSeasonStat::where('team_id', $this->teamId)
            ->distinct()
            ->pluck('season_year')
            ->sortDesc()
            ->values();

        if (!$this->availableSeasons->contains($this->selectedSeason)) {
            $this->selectedSeason = $this->availableSeasons->first() ?? '';
        }

        $this->loadStats();
        session()->flash('success', __('app.team_stat_deleted'));
    }

    public function getTotalWinsProperty()
    {
        return $this->seasonStats->sum('wins');
    }

    public function getTotalDrawsProperty()
    {
        return $this->seasonStats->sum('draws');
    }

    public function getTotalLossesProperty()
    {
        return $this->seasonStats->sum('losses');
    }

    public function getTotalGoalsForProperty()
    {
        return $this->seasonStats->sum('goals_for');
    }

    public function getTotalGoalsAgainstProperty()
    {
        return $this->seasonStats->sum('goals_against');
    }

    public function getAvgPossessionProperty()
    {
        $total = $this->seasonStats->sum('possession_avg');
        $count = $this->seasonStats->count();

        return $count > 0 ? round($total / $count, 1) : 0;
    }

    public function getTotalMatchesPlayedProperty()
    {
        return $this->seasonStats->sum('matches_played');
    }

    public function getTotalPointsProperty()
    {
        return $this->seasonStats->sum('points');
    }

    public function getTotalCleanSheetsProperty()
    {
        return $this->seasonStats->sum('clean_sheets');
    }

    public function getTotalYellowCardsProperty()
    {
        return $this->seasonStats->sum('yellow_cards');
    }

    public function getTotalRedCardsProperty()
    {
        return $this->seasonStats->sum('red_cards');
    }

    public function render()
    {
        return view('livewire.admin.teams.team-stats-page', [
            'title' => __('app.team_stats') . ' - ' . $this->team->name,
        ]);
    }
}
