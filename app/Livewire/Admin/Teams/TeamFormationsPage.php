<?php

namespace App\Livewire\Admin\Teams;

use App\Models\Formation;
use App\Models\Sport;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class TeamFormationsPage extends Component
{
    public $teamId;

    public $team;

    public $formations = [];

    public $showModal = false;

    public $editingFormationId = null;

    public $search = '';

    public $formationForm = [
        'name' => '',
        'sport_type' => 'football',
        'formation_code' => '4-4-2',
        'positions_data' => '[]',
        'description' => '',
        'is_default' => false,
    ];

    public function getSportTypesProperty(): array
    {
        return Sport::pluck('slug')->toArray();
    }

    public $footballFormations = [
        '4-4-2', '4-3-3', '3-5-2', '4-2-3-1', '5-3-2', '4-1-4-1', '3-4-3',
        '4-1-2-3', '2-3-5', '4-4-1-1', '4-3-2-1', '3-4-2-1', '4-2-2-2',
    ];

    public $futsalFormations = [
        '4-0', '3-1', '2-2', '1-2-1', '2-1-1', '1-1-2',
    ];

    public $selectedPositions = [];

    protected $listeners = ['closeModal'];

    public function mount(Team $team): void
    {
        $this->authorize('update', $team);

        $this->teamId = $team->id;
        $this->team = $team;
        $this->loadFormations();
    }

    public function updatedFormationFormSportType()
    {
        $this->formationForm['formation_code'] = $this->formationForm['sport_type'] === 'football'
            ? '4-4-2'
            : '4-0';
        $this->updatePositionsData();
    }

    public function updatedFormationFormFormationCode()
    {
        $this->updatePositionsData();
    }

    public function updatePositionsData()
    {
        $positions = $this->getDefaultPositions(
            $this->formationForm['formation_code'],
            $this->formationForm['sport_type']
        );
        $this->formationForm['positions_data'] = json_encode($positions);
        $this->selectedPositions = $positions;
    }

    public function loadFormations()
    {
        $this->formations = Formation::where('team_id', $this->teamId)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('formation_code', 'like', "%{$this->search}%");
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
        $this->editingFormationId = null;
        $positions = $this->getDefaultPositions('4-4-2', 'football');
        $this->formationForm = [
            'name' => '',
            'sport_type' => 'football',
            'formation_code' => '4-4-2',
            'positions_data' => json_encode($positions),
            'description' => '',
            'is_default' => false,
        ];
        $this->selectedPositions = $positions;
    }

    public function editFormation($id)
    {
        $formation = Formation::findOrFail($id);
        $this->editingFormationId = $id;
        $positions = is_array($formation->positions_data) ? $formation->positions_data : json_decode($formation->positions_data, true);
        $this->formationForm = [
            'name' => $formation->name,
            'sport_type' => $formation->sport_type,
            'formation_code' => $formation->formation_code,
            'positions_data' => json_encode($positions),
            'description' => $formation->description ?? '',
            'is_default' => $formation->is_default,
        ];
        $this->selectedPositions = $positions ?? [];
        $this->showModal = true;
    }

    public function saveFormation()
    {
        $this->validate([
            'formationForm.name' => 'required|string|max:255',
            'formationForm.sport_type' => 'required|in:'.implode(',', Sport::pluck('slug')->toArray()),
            'formationForm.formation_code' => 'required|string|max:20',
            'formationForm.positions_data' => 'required|json',
            'formationForm.description' => 'nullable|string',
            'formationForm.is_default' => 'boolean',
        ]);

        $positionsData = json_decode($this->formationForm['positions_data'], true);

        $sportIds = Sport::where('slug', $this->formationForm['sport_type'])->pluck('id');
        $data = [
            'name' => $this->formationForm['name'],
            'sport_type' => $this->formationForm['sport_type'],
            'sport_id' => $sportIds->first(),
            'formation_code' => $this->formationForm['formation_code'],
            'positions_data' => $positionsData,
            'description' => $this->formationForm['description'] ?: null,
            'is_default' => $this->formationForm['is_default'],
        ];

        if ($this->editingFormationId) {
            $formation = Formation::findOrFail($this->editingFormationId);
            if ($this->formationForm['is_default']) {
                Formation::where('team_id', $this->teamId)->update(['is_default' => false]);
            }
            $formation->update($data);
            session()->flash('success', __('app.formation_saved'));
        } else {
            if ($this->formationForm['is_default']) {
                Formation::where('team_id', $this->teamId)->update(['is_default' => false]);
            }
            Formation::create(array_merge($data, ['team_id' => $this->teamId]));
            session()->flash('success', __('app.formation_saved'));
        }

        $this->closeModal();
        $this->loadFormations();
    }

    public function deleteFormation($id)
    {
        $formation = Formation::findOrFail($id);
        $formation->delete();
        session()->flash('success', __('app.formation_deleted'));
        $this->loadFormations();
    }

    public function getDefaultPositions($code, $sportType)
    {
        $positions = [];

        if ($sportType === 'futsal') {
            $futsalFormations = [
                '4-0' => [['x' => 50, 'y' => 90, 'role' => 'GK'], ['x' => 20, 'y' => 65, 'role' => 'DEF'], ['x' => 40, 'y' => 65, 'role' => 'DEF'], ['x' => 60, 'y' => 65, 'role' => 'DEF'], ['x' => 80, 'y' => 65, 'role' => 'DEF']],
                '3-1' => [['x' => 50, 'y' => 90, 'role' => 'GK'], ['x' => 20, 'y' => 65, 'role' => 'DEF'], ['x' => 50, 'y' => 65, 'role' => 'DEF'], ['x' => 80, 'y' => 65, 'role' => 'DEF'], ['x' => 50, 'y' => 35, 'role' => 'PIVOT']],
                '2-2' => [['x' => 50, 'y' => 90, 'role' => 'GK'], ['x' => 30, 'y' => 65, 'role' => 'DEF'], ['x' => 70, 'y' => 65, 'role' => 'DEF'], ['x' => 30, 'y' => 35, 'role' => 'WING'], ['x' => 70, 'y' => 35, 'role' => 'WING']],
                '1-2-1' => [['x' => 50, 'y' => 90, 'role' => 'GK'], ['x' => 50, 'y' => 65, 'role' => 'DEF'], ['x' => 25, 'y' => 40, 'role' => 'WING'], ['x' => 75, 'y' => 40, 'role' => 'WING'], ['x' => 50, 'y' => 20, 'role' => 'PIVOT']],
                '2-1-1' => [['x' => 50, 'y' => 90, 'role' => 'GK'], ['x' => 30, 'y' => 65, 'role' => 'DEF'], ['x' => 70, 'y' => 65, 'role' => 'DEF'], ['x' => 50, 'y' => 40, 'role' => 'WING'], ['x' => 50, 'y' => 15, 'role' => 'PIVOT']],
                '1-1-2' => [['x' => 50, 'y' => 90, 'role' => 'GK'], ['x' => 50, 'y' => 65, 'role' => 'DEF'], ['x' => 50, 'y' => 40, 'role' => 'PIVOT'], ['x' => 25, 'y' => 20, 'role' => 'WING'], ['x' => 75, 'y' => 20, 'role' => 'WING']],
            ];

            return $futsalFormations[$code] ?? $futsalFormations['4-0'];
        }

        $parts = explode('-', $code);
        $gk = [['x' => 50, 'y' => 90, 'role' => 'GK']];
        $ySteps = [65, 45, 28, 15];
        $fieldWidth = 70;
        $offsetX = 15;

        $fieldPlayers = [];
        $playerIndex = 0;

        foreach ($parts as $lineIndex => $count) {
            $count = (int) $count;
            $y = $ySteps[$lineIndex] ?? 20;
            $positionsInLine = [];

            if ($count === 1) {
                $positionsInLine = [['x' => 50, 'y' => $y]];
            } else {
                $spacing = $fieldWidth / ($count - 1);
                for ($i = 0; $i < $count; $i++) {
                    $x = $offsetX + ($spacing * $i);
                    $positionsInLine[] = ['x' => round($x), 'y' => $y];
                }
            }

            $roles = ['DEF', 'DEF', 'MID', 'MID', 'FWD'];
            $role = $roles[$lineIndex] ?? 'MID';

            foreach ($positionsInLine as $pos) {
                $fieldPlayers[] = [
                    'x' => $pos['x'],
                    'y' => $pos['y'],
                    'role' => $role,
                    'index' => $playerIndex++,
                ];
            }
        }

        return array_merge($gk, $fieldPlayers);
    }

    public function updatedSearch()
    {
        $this->loadFormations();
    }

    public function render()
    {
        return view('livewire.admin.teams.team-formations-page', [
            'title' => __('app.formations').' - '.$this->team->name,
        ]);
    }
}
