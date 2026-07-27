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

    private static $statLabels = [
        'possession' => 'الاستحواذ',
        'shots_total' => 'التسديدات',
        'shots_on_target' => 'التسديدات على المرمى',
        'shots_off_target' => 'التسديدات خارج المرمى',
        'corners' => 'الركنيات',
        'fouls' => 'الأخطاء',
        'offsides' => 'التسلل',
        'yellow_cards' => 'البطاقات الصفراء',
        'red_cards' => 'البطاقات الحمراء',
        'passes_total' => 'التمريرات',
        'passes_accurate' => 'التمريرات الدقيقة',
        'tackles' => 'التدخلات',
        'saves' => 'التصديات',
        'hit_woodwork' => 'إصابة العارضة',
        'blocked_shots' => 'التسديدات المحجوبة',
    ];

    public function mount(Match_ $match): void
    {
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
            ? ($this->match->team1->name ?? 'الفريق الأول')
            : ($this->match->team2->name ?? 'الفريق الثاني');

        session()->flash('success', "تم حفظ إحصائيات {$teamName} بنجاح");
        $this->loadStats();
    }

    public function render()
    {
        $statFields = collect(self::$statLabels)->except(['possession']);

        return view('livewire.admin.matches.stats-page', [
            'title' => 'إحصائيات المباراة - ' . $this->match->team1->name . ' vs ' . $this->match->team2->name,
            'match' => $this->match,
            'team1Stats' => $this->team1Stats,
            'team2Stats' => $this->team2Stats,
            'statLabels' => self::$statLabels,
            'statFields' => $statFields,
        ]);
    }
}
