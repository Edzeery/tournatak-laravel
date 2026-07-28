<?php

namespace App\Livewire\Admin\Teams;

use App\Models\Team;
use App\Models\TeamMedicalRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class TeamMedicalPage extends Component
{
    public $teamId;
    public $team;
    public $medicalRecords = [];
    public $players = [];
    public $showModal = false;
    public $editingRecordId = null;
    public $search = '';
    public $filterStatus = '';
    public $filterType = '';

    public $recordForm = [
        'player_id' => null,
        'record_type' => 'injury',
        'injury_name' => '',
        'severity' => 'minor',
        'status' => 'active',
        'injury_date' => '',
        'expected_return' => '',
        'treatment' => '',
        'notes' => '',
    ];

    public static function getRecordTypes(): array
    {
        return [
            'injury' => __('app.sub_injury'),
            'illness' => __('app.disease'),
            'checkup' => __('app.medical_checkup'),
            'surgery' => __('app.surgery'),
            'rehabilitation' => __('app.rehabilitation'),
        ];
    }

    public static function getSeverityLevels(): array
    {
        return [
            'minor' => __('app.severity_minor'),
            'moderate' => __('app.severity_moderate'),
            'severe' => __('app.severity_severe'),
            'critical' => __('app.severity_critical'),
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'active' => __('app.active_status'),
            'recovering' => __('app.recovering_status'),
            'returned' => __('app.returned_status'),
            'long_term' => __('app.long_term_status'),
        ];
    }

    protected $listeners = ['closeModal'];

    public function mount(Team $team): void
    {
        $this->authorize('update', $team);

        $this->teamId = $team->id;
        $this->team = $team;
        $this->loadRecords();
        $this->players = $this->team->players()->with('user')->get();
    }

    public function loadRecords()
    {
        $this->medicalRecords = TeamMedicalRecord::with('player.user')
            ->where('team_id', $this->teamId)
            ->when($this->search, function ($q) {
                $q->where('injury_name', 'like', "%{$this->search}%")
                    ->orWhereHas('player.user', function ($uq) {
                        $uq->where('name', 'like', "%{$this->search}%");
                    });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterType, function ($q) {
                $q->where('record_type', $this->filterType);
            })
            ->latest('injury_date')
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
        $this->editingRecordId = null;
        $this->recordForm = [
            'player_id' => null,
            'record_type' => 'injury',
            'injury_name' => '',
            'severity' => 'minor',
            'status' => 'active',
            'injury_date' => '',
            'expected_return' => '',
            'treatment' => '',
            'notes' => '',
        ];
    }

    public function editRecord($id)
    {
        $record = TeamMedicalRecord::findOrFail($id);
        $this->editingRecordId = $id;
        $this->recordForm = [
            'player_id' => $record->player_id,
            'record_type' => $record->record_type,
            'injury_name' => $record->injury_name,
            'severity' => $record->severity,
            'status' => $record->status,
            'injury_date' => $record->injury_date ? $record->injury_date->format('Y-m-d') : '',
            'expected_return' => $record->expected_return ? $record->expected_return->format('Y-m-d') : '',
            'treatment' => $record->treatment ?? '',
            'notes' => $record->notes ?? '',
        ];
        $this->showModal = true;
    }

    public function saveRecord()
    {
        $this->validate([
            'recordForm.player_id' => 'required|exists:players,id',
            'recordForm.record_type' => 'required|in:injury,illness,checkup,surgery,rehabilitation',
            'recordForm.injury_name' => 'required|string|max:255',
            'recordForm.severity' => 'required|in:minor,moderate,severe,critical',
            'recordForm.status' => 'required|in:active,recovering,returned,long_term',
            'recordForm.injury_date' => 'required|date',
            'recordForm.expected_return' => 'nullable|date|after_or_equal:recordForm.injury_date',
            'recordForm.treatment' => 'nullable|string',
            'recordForm.notes' => 'nullable|string',
        ]);

        $data = [
            'player_id' => $this->recordForm['player_id'],
            'record_type' => $this->recordForm['record_type'],
            'injury_name' => $this->recordForm['injury_name'],
            'severity' => $this->recordForm['severity'],
            'status' => $this->recordForm['status'],
            'injury_date' => $this->recordForm['injury_date'],
            'expected_return' => $this->recordForm['expected_return'] ?: null,
            'treatment' => $this->recordForm['treatment'] ?: null,
            'notes' => $this->recordForm['notes'] ?: null,
            'reported_by' => Auth::id(),
        ];

        if ($this->editingRecordId) {
            $record = TeamMedicalRecord::findOrFail($this->editingRecordId);
            $record->update($data);
            session()->flash('success', __('app.medical_record_saved'));
        } else {
            TeamMedicalRecord::create(array_merge($data, ['team_id' => $this->teamId]));
            session()->flash('success', __('app.medical_record_saved'));
        }

        $this->closeModal();
        $this->loadRecords();
    }

    public function deleteRecord($id)
    {
        $record = TeamMedicalRecord::findOrFail($id);
        $record->delete();
        session()->flash('success', __('app.medical_record_deleted'));
        $this->loadRecords();
    }

    public function updatedSearch()
    {
        $this->loadRecords();
    }

    public function updatedFilterStatus()
    {
        $this->loadRecords();
    }

    public function updatedFilterType()
    {
        $this->loadRecords();
    }

    public function render()
    {
        return view('livewire.admin.teams.team-medical-page', [
            'title' => __('app.medical_record') . ' - ' . $this->team->name,
            'recordTypes' => self::getRecordTypes(),
            'severityLevels' => self::getSeverityLevels(),
            'statusOptions' => self::getStatusOptions(),
        ]);
    }
}
