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

    public const RECORD_TYPES = [
        'injury' => 'إصابة',
        'illness' => 'مرض',
        'checkup' => 'فحص طبي',
        'surgery' => 'جراحة',
        'rehabilitation' => 'تأهيل',
    ];

    public const SEVERITY_LEVELS = [
        'minor' => 'طفيف',
        'moderate' => 'متوسط',
        'severe' => 'شديد',
        'critical' => 'حرج',
    ];

    public const STATUS_OPTIONS = [
        'active' => 'نشط',
        'recovering' => 'تعافي',
        'returned' => 'عاد',
        'long_term' => 'إصابات طويلة',
    ];

    protected $listeners = ['closeModal'];

    public function mount(Team $team): void
    {
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
            session()->flash('success', 'تم تحديث السجل الطبي بنجاح');
        } else {
            TeamMedicalRecord::create(array_merge($data, ['team_id' => $this->teamId]));
            session()->flash('success', 'تم إضافة السجل الطبي بنجاح');
        }

        $this->closeModal();
        $this->loadRecords();
    }

    public function deleteRecord($id)
    {
        $record = TeamMedicalRecord::findOrFail($id);
        $record->delete();
        session()->flash('success', 'تم حذف السجل الطبي بنجاح');
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
            'title' => 'السجلات الطبية - ' . $this->team->name,
            'recordTypes' => self::RECORD_TYPES,
            'severityLevels' => self::SEVERITY_LEVELS,
            'statusOptions' => self::STATUS_OPTIONS,
        ]);
    }
}
