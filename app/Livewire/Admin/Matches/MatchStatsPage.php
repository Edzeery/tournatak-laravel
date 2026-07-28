<?php

namespace App\Livewire\Admin\Matches;

use App\Models\Match_;
use App\Models\MatchStat;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class MatchStatsPage extends Component
{
    public $matchId;
    public $match;
    public $team1Stats;
    public $team2Stats;
    public $activeTeam = 1;

    public $statsForm = [
        'possession' => 50.0,
        'shots_total' => 0,
        'shots_on_target' => 0,
        'shots_off_target' => 0,
        'corners' => 0,
        'fouls' => 0,
        'offsides' => 0,
        'yellow_cards' => 0,
        'red_cards' => 0,
        'passes_total' => 0,
        'passes_accurate' => 0,
        'tackles' => 0,
        'saves' => 0,
        'hit_woodwork' => 0,
        'blocked_shots' => 0,
    ];

    public $statsForm2 = [
        'possession' => 50.0,
        'shots_total' => 0,
        'shots_on_target' => 0,
        'shots_off_target' => 0,
        'corners' => 0,
        'fouls' => 0,
        'offsides' => 0,
        'yellow_cards' => 0,
        'red_cards' => 0,
        'passes_total' => 0,
        'passes_accurate' => 0,
        'tackles' => 0,
        'saves' => 0,
        'hit_woodwork' => 0,
        'blocked_shots' => 0,
    ];

    private static function getStatLabels(): array
    {
        return [
            'possession' => __('app.stat_possession'),
            'shots_total' => __('app.stat_shots_total'),
            'shots_on_target' => __('app.stat_shots_on_target'),
            'shots_off_target' => __('app.stat_shots_off_target'),
            'corners' => __('app.stat_corners'),
            'fouls' => __('app.stat_fouls'),
            'offsides' => __('app.stat_offsides'),
            'yellow_cards' => __('app.stat_yellow_cards'),
            'red_cards' => __('app.stat_red_cards'),
            'passes_total' => __('app.stat_passes_total'),
            'passes_accurate' => __('app.stat_passes_accurate'),
            'tackles' => __('app.stat_tackles'),
            'saves' => __('app.stat_saves'),
            'hit_woodwork' => __('app.stat_hit_woodwork'),
            'blocked_shots' => __('app.stat_blocked_shots'),
        ];
    }

    public function mount(Match_ $match): void
    {
        $this->authorize('update', $match);

        $this->matchId = $match->id;
        $this->match = $match->load(['team1', 'team2']);
        $this->loadStats();
    }

    public function switchTeam($teamNum): void
    {
        $this->activeTeam = $teamNum;
    }

    public function loadStats(): void
    {
        $this->team1Stats = MatchStat::where('match_id', $this->matchId)
            ->where('team_id', $this->match->team1_id)
            ->first();

        $this->team2Stats = MatchStat::where('match_id', $this->matchId)
            ->where('team_id', $this->match->team2_id)
            ->first();

        $fields = array_keys($this->statsForm);

        foreach ($fields as $field) {
            $this->statsForm[$field] = $this->team1Stats->$field ?? ($field === 'possession' ? 50.0 : 0);
            $this->statsForm2[$field] = $this->team2Stats->$field ?? ($field === 'possession' ? 50.0 : 0);
        }
    }

    public function saveStats(): void
    {
        $formKey = $this->activeTeam === 1 ? 'statsForm' : 'statsForm2';
        $teamId = $this->activeTeam === 1 ? $this->match->team1_id : $this->match->team2_id;

        $this->validate([
            "{$formKey}.possession" => 'required|numeric|min:0|max:100',
            "{$formKey}.shots_total" => 'required|integer|min:0',
            "{$formKey}.shots_on_target" => 'required|integer|min:0',
            "{$formKey}.shots_off_target" => 'required|integer|min:0',
            "{$formKey}.corners" => 'required|integer|min:0',
            "{$formKey}.fouls" => 'required|integer|min:0',
            "{$formKey}.offsides" => 'required|integer|min:0',
            "{$formKey}.yellow_cards" => 'required|integer|min:0',
            "{$formKey}.red_cards" => 'required|integer|min:0',
            "{$formKey}.passes_total" => 'required|integer|min:0',
            "{$formKey}.passes_accurate" => 'required|integer|min:0',
            "{$formKey}.tackles" => 'required|integer|min:0',
            "{$formKey}.saves" => 'required|integer|min:0',
            "{$formKey}.hit_woodwork" => 'required|integer|min:0',
            "{$formKey}.blocked_shots" => 'required|integer|min:0',
        ]);

        $data = [
            'possession' => $this->{$formKey}['possession'],
            'shots_total' => $this->{$formKey}['shots_total'],
            'shots_on_target' => $this->{$formKey}['shots_on_target'],
            'shots_off_target' => $this->{$formKey}['shots_off_target'],
            'corners' => $this->{$formKey}['corners'],
            'fouls' => $this->{$formKey}['fouls'],
            'offsides' => $this->{$formKey}['offsides'],
            'yellow_cards' => $this->{$formKey}['yellow_cards'],
            'red_cards' => $this->{$formKey}['red_cards'],
            'passes_total' => $this->{$formKey}['passes_total'],
            'passes_accurate' => $this->{$formKey}['passes_accurate'],
            'tackles' => $this->{$formKey}['tackles'],
            'saves' => $this->{$formKey}['saves'],
            'hit_woodwork' => $this->{$formKey}['hit_woodwork'],
            'blocked_shots' => $this->{$formKey}['blocked_shots'],
        ];

        MatchStat::updateOrCreate(
            [
                'match_id' => $this->matchId,
                'team_id' => $teamId,
            ],
            $data
        );

        $teamName = $this->activeTeam === 1
            ? ($this->match->team1->name ?? __('app.team1_name'))
            : ($this->match->team2->name ?? __('app.team2_name'));

        session()->flash('success', __('app.stats_saved_for', ['team' => $teamName]));
        $this->loadStats();
    }

    public function render()
    {
        $statFields = collect(self::getStatLabels())->except(['possession']);

        return view('livewire.admin.matches.stats-page', [
            'title' => __('app.page_title_match_stats') . ' - ' . $this->match->team1->name . ' vs ' . $this->match->team2->name,
            'match' => $this->match,
            'team1Stats' => $this->team1Stats,
            'team2Stats' => $this->team2Stats,
            'statLabels' => self::getStatLabels(),
            'statFields' => $statFields,
        ]);
    }
}
