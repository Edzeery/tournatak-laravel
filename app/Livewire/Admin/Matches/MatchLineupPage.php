<?php

namespace App\Livewire\Admin\Matches;

use App\Models\Match_;
use App\Models\MatchLineup;
use App\Models\Player;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class MatchLineupPage extends Component
{
    public $matchId;
    public $match;
    public $team1Lineup = [];
    public $team2Lineup = [];
    public bool $showModal = false;
    public ?int $editingLineupId = null;
    public int $activeTeam = 1;

    public $lineupForm = [
        'player_id' => null,
        'position_id' => null,
        'is_starter' => true,
        'jersey_number' => null,
        'minute_in' => null,
        'minute_out' => null,
        'sub_reason' => null,
        'is_captain' => false,
        'performance_notes' => null,
    ];

    private static $subReasons = [
        'tactical' => 'تكتيكي',
        'injury' => 'إصابة',
        'red_card' => 'بطاقة حمراء',
        'yellow_card' => 'بطاقة صفراء',
        'fatigue' => 'إرهاق',
    ];

    public $selectedFormation1 = null;
    public $selectedFormation2 = null;
    public $formationsList = [];
    public $showFormationSelector = false;
    public $activeFormationTeam = 1;

    private static $formationPositions = [
        '4-4-2' => [
            ['x' => 50, 'y' => 90, 'position' => 'GK'],
            ['x' => 80, 'y' => 72, 'position' => 'RB'],
            ['x' => 60, 'y' => 75, 'position' => 'CB'],
            ['x' => 40, 'y' => 75, 'position' => 'CB'],
            ['x' => 20, 'y' => 72, 'position' => 'LB'],
            ['x' => 82, 'y' => 52, 'position' => 'RM'],
            ['x' => 60, 'y' => 52, 'position' => 'CM'],
            ['x' => 40, 'y' => 52, 'position' => 'CM'],
            ['x' => 18, 'y' => 52, 'position' => 'LM'],
            ['x' => 62, 'y' => 28, 'position' => 'ST'],
            ['x' => 38, 'y' => 28, 'position' => 'ST'],
        ],
        '4-3-3' => [
            ['x' => 50, 'y' => 90, 'position' => 'GK'],
            ['x' => 80, 'y' => 72, 'position' => 'RB'],
            ['x' => 60, 'y' => 75, 'position' => 'CB'],
            ['x' => 40, 'y' => 75, 'position' => 'CB'],
            ['x' => 20, 'y' => 72, 'position' => 'LB'],
            ['x' => 62, 'y' => 55, 'position' => 'CM'],
            ['x' => 50, 'y' => 52, 'position' => 'CM'],
            ['x' => 38, 'y' => 55, 'position' => 'CM'],
            ['x' => 82, 'y' => 30, 'position' => 'RW'],
            ['x' => 50, 'y' => 25, 'position' => 'ST'],
            ['x' => 18, 'y' => 30, 'position' => 'LW'],
        ],
        '4-2-3-1' => [
            ['x' => 50, 'y' => 90, 'position' => 'GK'],
            ['x' => 80, 'y' => 72, 'position' => 'RB'],
            ['x' => 60, 'y' => 75, 'position' => 'CB'],
            ['x' => 40, 'y' => 75, 'position' => 'CB'],
            ['x' => 20, 'y' => 72, 'position' => 'LB'],
            ['x' => 62, 'y' => 58, 'position' => 'CDM'],
            ['x' => 38, 'y' => 58, 'position' => 'CDM'],
            ['x' => 78, 'y' => 42, 'position' => 'RAM'],
            ['x' => 50, 'y' => 40, 'position' => 'CAM'],
            ['x' => 22, 'y' => 42, 'position' => 'LAM'],
            ['x' => 50, 'y' => 22, 'position' => 'ST'],
        ],
        '3-5-2' => [
            ['x' => 50, 'y' => 90, 'position' => 'GK'],
            ['x' => 65, 'y' => 75, 'position' => 'CB'],
            ['x' => 50, 'y' => 78, 'position' => 'CB'],
            ['x' => 35, 'y' => 75, 'position' => 'CB'],
            ['x' => 88, 'y' => 55, 'position' => 'RWB'],
            ['x' => 62, 'y' => 52, 'position' => 'CM'],
            ['x' => 50, 'y' => 50, 'position' => 'CM'],
            ['x' => 38, 'y' => 52, 'position' => 'CM'],
            ['x' => 12, 'y' => 55, 'position' => 'LWB'],
            ['x' => 60, 'y' => 28, 'position' => 'ST'],
            ['x' => 40, 'y' => 28, 'position' => 'ST'],
        ],
        '5-3-2' => [
            ['x' => 50, 'y' => 90, 'position' => 'GK'],
            ['x' => 85, 'y' => 68, 'position' => 'RWB'],
            ['x' => 65, 'y' => 75, 'position' => 'CB'],
            ['x' => 50, 'y' => 78, 'position' => 'CB'],
            ['x' => 35, 'y' => 75, 'position' => 'CB'],
            ['x' => 15, 'y' => 68, 'position' => 'LWB'],
            ['x' => 62, 'y' => 52, 'position' => 'CM'],
            ['x' => 50, 'y' => 50, 'position' => 'CM'],
            ['x' => 38, 'y' => 52, 'position' => 'CM'],
            ['x' => 60, 'y' => 28, 'position' => 'ST'],
            ['x' => 40, 'y' => 28, 'position' => 'ST'],
        ],
        '4-1-4-1' => [
            ['x' => 50, 'y' => 90, 'position' => 'GK'],
            ['x' => 80, 'y' => 72, 'position' => 'RB'],
            ['x' => 60, 'y' => 75, 'position' => 'CB'],
            ['x' => 40, 'y' => 75, 'position' => 'CB'],
            ['x' => 20, 'y' => 72, 'position' => 'LB'],
            ['x' => 50, 'y' => 58, 'position' => 'CDM'],
            ['x' => 80, 'y' => 45, 'position' => 'RM'],
            ['x' => 60, 'y' => 48, 'position' => 'CM'],
            ['x' => 40, 'y' => 48, 'position' => 'CM'],
            ['x' => 20, 'y' => 45, 'position' => 'LM'],
            ['x' => 50, 'y' => 25, 'position' => 'ST'],
        ],
    ];

    public function mount(Match_ $match): void
    {
        $this->matchId = $match->id;
        $this->match = $match->load(['team1', 'team2']);
        $this->formationsList = [
            '4-4-2' => '4-4-2',
            '4-3-3' => '4-3-3',
            '4-2-3-1' => '4-2-3-1',
            '3-5-2' => '3-5-2',
            '5-3-2' => '5-3-2',
            '4-1-4-1' => '4-1-4-1',
        ];
        $this->selectedFormation1 = '4-4-2';
        $this->selectedFormation2 = '4-4-2';
        $this->loadLineups();
    }

    public function loadLineups()
    {
        $this->team1Lineup = MatchLineup::with(['player.user', 'position'])
            ->where('match_id', $this->matchId)
            ->where('team_id', $this->match->team1_id)
            ->orderBy('is_starter', 'desc')
            ->orderBy('jersey_number')
            ->get();

        $this->team2Lineup = MatchLineup::with(['player.user', 'position'])
            ->where('match_id', $this->matchId)
            ->where('team_id', $this->match->team2_id)
            ->orderBy('is_starter', 'desc')
            ->orderBy('jersey_number')
            ->get();
    }

    public function switchTeamAndOpen($teamNum)
    {
        $this->activeTeam = $teamNum;
        $this->resetForm();
        $this->showModal = true;
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    private function resetForm()
    {
        $this->editingLineupId = null;
        $this->lineupForm = [
            'player_id' => null,
            'position_id' => null,
            'is_starter' => true,
            'jersey_number' => null,
            'minute_in' => null,
            'minute_out' => null,
            'sub_reason' => null,
            'is_captain' => false,
            'performance_notes' => null,
        ];
    }

    public function saveLineup()
    {
        $this->validate([
            'lineupForm.player_id' => 'required|exists:players,id',
            'lineupForm.position_id' => 'required|exists:positions,id',
            'lineupForm.is_starter' => 'required|boolean',
            'lineupForm.jersey_number' => 'nullable|integer|min:0|max:99',
            'lineupForm.minute_in' => 'nullable|integer|min:0|max:120',
            'lineupForm.minute_out' => 'nullable|integer|min:0|max:120',
            'lineupForm.sub_reason' => 'nullable|in:' . implode(',', array_keys(self::$subReasons)),
            'lineupForm.is_captain' => 'required|boolean',
        ]);

        $teamId = $this->activeTeam === 1 ? $this->match->team1_id : $this->match->team2_id;

        $player = Player::findOrFail($this->lineupForm['player_id']);
        if ($player->team_id !== $teamId) {
            session()->flash('error', 'اللاعب لا ينتمي لهذا الفريق');
            return;
        }

        if ($this->editingLineupId) {
            $lineup = MatchLineup::findOrFail($this->editingLineupId);
            $lineup->update([
                'player_id' => $this->lineupForm['player_id'],
                'position_id' => $this->lineupForm['position_id'],
                'is_starter' => $this->lineupForm['is_starter'],
                'jersey_number' => $this->lineupForm['jersey_number'],
                'minute_in' => $this->lineupForm['minute_in'],
                'minute_out' => $this->lineupForm['minute_out'],
                'sub_reason' => $this->lineupForm['sub_reason'],
                'is_captain' => $this->lineupForm['is_captain'],
                'performance_notes' => $this->lineupForm['performance_notes'],
            ]);
            session()->flash('success', 'تم تحديث التشكيلة بنجاح');
        } else {
            MatchLineup::create([
                'match_id' => $this->matchId,
                'team_id' => $teamId,
                'player_id' => $this->lineupForm['player_id'],
                'position_id' => $this->lineupForm['position_id'],
                'is_starter' => $this->lineupForm['is_starter'],
                'jersey_number' => $this->lineupForm['jersey_number'],
                'minute_in' => $this->lineupForm['minute_in'],
                'minute_out' => $this->lineupForm['minute_out'],
                'sub_reason' => $this->lineupForm['sub_reason'],
                'is_captain' => $this->lineupForm['is_captain'],
                'performance_notes' => $this->lineupForm['performance_notes'],
            ]);
            session()->flash('success', 'تم إضافة التشكيلة بنجاح');
        }

        $this->closeModal();
        $this->loadLineups();
    }

    public function editLineup($id)
    {
        $lineup = MatchLineup::findOrFail($id);

        if ($lineup->team_id === $this->match->team1_id) {
            $this->activeTeam = 1;
        } else {
            $this->activeTeam = 2;
        }

        $this->editingLineupId = $id;
        $this->lineupForm = [
            'player_id' => $lineup->player_id,
            'position_id' => $lineup->position_id,
            'is_starter' => $lineup->is_starter,
            'jersey_number' => $lineup->jersey_number,
            'minute_in' => $lineup->minute_in,
            'minute_out' => $lineup->minute_out,
            'sub_reason' => $lineup->sub_reason,
            'is_captain' => $lineup->is_captain,
            'performance_notes' => $lineup->performance_notes ?? null,
        ];
        $this->showModal = true;
    }

    public function deleteLineup($id)
    {
        MatchLineup::findOrFail($id)->delete();
        session()->flash('success', 'تم حذف التشكيلة بنجاح');
        $this->loadLineups();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingLineupId = null;
        $this->lineupForm = [
            'player_id' => null,
            'position_id' => null,
            'is_starter' => true,
            'jersey_number' => null,
            'minute_in' => null,
            'minute_out' => null,
            'sub_reason' => null,
            'is_captain' => false,
            'performance_notes' => null,
        ];
    }

    public function getPitchData($teamNum): array
    {
        $lineup = $teamNum === 1 ? $this->team1Lineup : $this->team2Lineup;
        $starters = $lineup->filter(fn($l) => $l->is_starter);
        $formation = $teamNum === 1 ? $this->selectedFormation1 : $this->selectedFormation2;

        $positions = self::$formationPositions[$formation] ?? self::$formationPositions['4-4-2'];
        $result = [];

        foreach ($starters as $idx => $player) {
            $pos = $positions[$idx] ?? ['x' => 50, 'y' => 50, 'position' => '?'];
            $result[] = [
                'x' => $pos['x'],
                'y' => $pos['y'],
                'position' => $player->position->abbreviation ?? $pos['position'],
                'player_name' => $player->player->name ?? '',
                'jersey_number' => $player->jersey_number ?? '',
                'is_captain' => $player->is_captain,
                'lineup_id' => $player->id,
            ];
        }

        return $result;
    }

    public function selectFormation($teamNum, $code): void
    {
        if ($teamNum === 1) {
            $this->selectedFormation1 = $code;
        } else {
            $this->selectedFormation2 = $code;
        }
    }

    public function openFormationSelector($teamNum): void
    {
        $this->activeFormationTeam = $teamNum;
        $this->showFormationSelector = true;
    }

    public function render()
    {
        $teamId = $this->activeTeam === 1 ? $this->match->team1_id : $this->match->team2_id;
        $players = Player::where('team_id', $teamId)->orderBy('number')->get();
        $positions = Position::where('sport_type', 'football')->where('is_active', true)->orderBy('sort_order')->get();

        return view('livewire.admin.matches.lineup-page', [
            'title' => 'إدارة التشكيلة - ' . $this->match->team1->name . ' vs ' . $this->match->team2->name,
            'match' => $this->match,
            'team1Lineup' => $this->team1Lineup,
            'team2Lineup' => $this->team2Lineup,
            'players' => $players,
            'positions' => $positions,
            'subReasons' => self::$subReasons,
            'pitchData1' => $this->getPitchData(1),
            'pitchData2' => $this->getPitchData(2),
        ]);
    }
}
