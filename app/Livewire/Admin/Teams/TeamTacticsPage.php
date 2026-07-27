<?php

namespace App\Livewire\Admin\Teams;

use App\Models\Team;
use App\Models\TeamTactic;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class TeamTacticsPage extends Component
{
    public $teamId;
    public $team;
    public $tactics = [];
    public $showModal = false;
    public $editingTacticId = null;
    public $search = '';

    public $tacticForm = [
        'name' => '',
        'pressing_style' => 'medium',
        'build_up_style' => 'mixed',
        'defense_style' => 'zone',
        'attack_style' => 'balanced',
        'formation_used' => '',
        'notes' => '',
        'is_default' => false,
    ];

    public const PRESSING_STYLES = [
        'high' => 'ضغط عالي',
        'medium' => 'ضغط متوسط',
        'low' => 'ضغط منخفض',
        'mixed' => 'مختلط',
    ];

    public const BUILD_UP_STYLES = [
        'from_back' => 'بناء من الخلف',
        'quick_counter' => 'هجمات سريعة',
        'long_ball' => 'كرة طويلة',
        'mixed' => 'مختلط',
    ];

    public const DEFENSE_STYLES = [
        'zone' => 'دفاع مناطقي',
        'man_to_man' => 'دفاع شخصي',
        'mixed' => 'مختلط',
    ];

    public const ATTACK_STYLES = [
        'wing_play' => 'هجوم أجنحة',
        'central' => 'هجوم مركزي',
        'balanced' => 'هجوم متوازن',
        'counter_attack' => 'هجوم مضاد',
    ];

    public const FORMATION_OPTIONS = [
        '4-4-2', '4-3-3', '3-5-2', '4-2-3-1', '5-3-2', '4-1-4-1', '3-4-3',
        '4-1-2-3', '2-3-5', '4-4-1-1', '4-3-2-1', '3-4-2-1', '4-2-2-2',
        '4-0', '3-1', '2-2', '1-2-1', '2-1-1', '1-1-2',
    ];

    protected $listeners = ['closeModal'];

    public function mount(Team $team): void
    {
        $this->teamId = $team->id;
        $this->team = $team;
        $this->loadTactics();
    }

    public function loadTactics()
    {
        $this->tactics = TeamTactic::where('team_id', $this->teamId)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('formation_used', 'like', "%{$this->search}%");
            })
            ->latest()
            ->get();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingTacticId = null;
        $this->tacticForm = [
            'name' => '',
            'pressing_style' => 'medium',
            'build_up_style' => 'mixed',
            'defense_style' => 'zone',
            'attack_style' => 'balanced',
            'formation_used' => '',
            'notes' => '',
            'is_default' => false,
        ];
    }

    public function editTactic($id)
    {
        $tactic = TeamTactic::findOrFail($id);
        $this->editingTacticId = $id;
        $this->tacticForm = [
            'name' => $tactic->name,
            'pressing_style' => $tactic->pressing_style,
            'build_up_style' => $tactic->build_up_style,
            'defense_style' => $tactic->defense_style,
            'attack_style' => $tactic->attack_style,
            'formation_used' => $tactic->formation_used ?? '',
            'notes' => $tactic->notes ?? '',
            'is_default' => $tactic->is_default,
        ];
        $this->showModal = true;
    }

    public function saveTactic()
    {
        $this->validate([
            'tacticForm.name' => 'required|string|max:255',
            'tacticForm.pressing_style' => 'required|in:high,medium,low,mixed',
            'tacticForm.build_up_style' => 'required|in:from_back,quick_counter,long_ball,mixed',
            'tacticForm.defense_style' => 'required|in:zone,man_to_man,mixed',
            'tacticForm.attack_style' => 'required|in:wing_play,central,balanced,counter_attack',
            'tacticForm.formation_used' => 'nullable|in:4-4-2,4-3-3,3-5-2,4-2-3-1,5-3-2,4-1-4-1,3-4-3,4-1-2-3,2-3-5,4-4-1-1,4-3-2-1,3-4-2-1,4-2-2-2,4-0,3-1,2-2,1-2-1,2-1-1,1-1-2',
            'tacticForm.notes' => 'nullable|string',
            'tacticForm.is_default' => 'boolean',
        ]);

        $data = [
            'name' => $this->tacticForm['name'],
            'pressing_style' => $this->tacticForm['pressing_style'],
            'build_up_style' => $this->tacticForm['build_up_style'],
            'defense_style' => $this->tacticForm['defense_style'],
            'attack_style' => $this->tacticForm['attack_style'],
            'formation_used' => $this->tacticForm['formation_used'] ?: null,
            'notes' => $this->tacticForm['notes'] ?: null,
            'is_default' => $this->tacticForm['is_default'],
        ];

        if ($this->editingTacticId) {
            $tactic = TeamTactic::findOrFail($this->editingTacticId);
            if ($this->tacticForm['is_default']) {
                TeamTactic::where('team_id', $this->teamId)->update(['is_default' => false]);
            }
            $tactic->update($data);
            session()->flash('success', 'تم تحديث التكتيك بنجاح');
        } else {
            if ($this->tacticForm['is_default']) {
                TeamTactic::where('team_id', $this->teamId)->update(['is_default' => false]);
            }
            TeamTactic::create(array_merge($data, ['team_id' => $this->teamId]));
            session()->flash('success', 'تم إضافة التكتيك بنجاح');
        }

        $this->closeModal();
        $this->loadTactics();
    }

    public function deleteTactic($id)
    {
        $tactic = TeamTactic::findOrFail($id);
        $tactic->delete();
        session()->flash('success', 'تم حذف التكتيك بنجاح');
        $this->loadTactics();
    }

    public function updatedSearch()
    {
        $this->loadTactics();
    }

    public function render()
    {
        return view('livewire.admin.teams.team-tactics-page', [
            'title' => 'التكتيكات - ' . $this->team->name,
            'pressingStyles' => self::PRESSING_STYLES,
            'buildUpStyles' => self::BUILD_UP_STYLES,
            'defenseStyles' => self::DEFENSE_STYLES,
            'attackStyles' => self::ATTACK_STYLES,
            'formationOptions' => self::FORMATION_OPTIONS,
        ]);
    }
}
