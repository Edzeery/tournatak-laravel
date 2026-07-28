<?php

namespace App\Livewire\Admin\Teams;

use App\Models\Team;
use App\Models\TeamStaff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class TeamStaffPage extends Component
{
    public $teamId;
    public $team;
    public $staff = [];
    public $showModal = false;
    public $editingStaffId = null;
    public $search = '';

    public $staffForm = [
        'user_id' => null,
        'staff_role' => '',
        'specialization' => '',
        'start_date' => '',
        'end_date' => '',
    ];

    public $userSearch = '';
    public $searchedUsers = [];

    protected $listeners = ['closeModal'];

    public function mount(Team $team): void
    {
        $this->authorize('update', $team);

        $this->teamId = $team->id;
        $this->team = $team;
        $this->loadStaff();
    }

    public function updatedUserSearch()
    {
        $this->searchedUsers = DB::table('users')
            ->where('name', 'like', "%{$this->userSearch}%")
            ->orWhere('username', 'like', "%{$this->userSearch}%")
            ->limit(10)
            ->get();
    }

    public function selectUser($userId)
    {
        $user = DB::table('users')->find($userId);
        if ($user) {
            $this->staffForm['user_id'] = $userId;
            $this->userSearch = $user->name;
            $this->searchedUsers = [];
        }
    }

    public function loadStaff()
    {
        $this->staff = TeamStaff::with('user')
            ->where('team_id', $this->teamId)
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($uq) {
                    $uq->where('name', 'like', "%{$this->search}%")
                        ->orWhere('username', 'like', "%{$this->search}%");
                });
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
        $this->editingStaffId = null;
        $this->staffForm = [
            'user_id' => null,
            'staff_role' => '',
            'specialization' => '',
            'start_date' => '',
            'end_date' => '',
        ];
        $this->userSearch = '';
        $this->searchedUsers = [];
    }

    public function editStaff($id)
    {
        $record = TeamStaff::findOrFail($id);
        $this->editingStaffId = $id;
        $this->staffForm = [
            'user_id' => $record->user_id,
            'staff_role' => $record->staff_role,
            'specialization' => $record->specialization ?? '',
            'start_date' => $record->start_date ? $record->start_date->format('Y-m-d') : '',
            'end_date' => $record->end_date ? $record->end_date->format('Y-m-d') : '',
        ];
        if ($record->user) {
            $this->userSearch = $record->user->name;
        }
        $this->showModal = true;
    }

    public function saveStaff()
    {
        $this->validate([
            'staffForm.user_id' => 'required|exists:users,id',
            'staffForm.staff_role' => 'required|in:head_coach,assistant_coach,goalkeeping_coach,fitness_coach,doctor,physiotherapist,admin,manager,nutritionist,analyst',
            'staffForm.specialization' => 'nullable|string|max:255',
            'staffForm.start_date' => 'nullable|date',
            'staffForm.end_date' => 'nullable|date|after_or_equal:staffForm.start_date',
        ]);

        if ($this->editingStaffId) {
            $record = TeamStaff::findOrFail($this->editingStaffId);
            $record->update([
                'user_id' => $this->staffForm['user_id'],
                'staff_role' => $this->staffForm['staff_role'],
                'specialization' => $this->staffForm['specialization'] ?: null,
                'start_date' => $this->staffForm['start_date'] ?: null,
                'end_date' => $this->staffForm['end_date'] ?: null,
            ]);
            session()->flash('success', __('app.staff_saved'));
        } else {
            TeamStaff::create([
                'team_id' => $this->teamId,
                'user_id' => $this->staffForm['user_id'],
                'staff_role' => $this->staffForm['staff_role'],
                'specialization' => $this->staffForm['specialization'] ?: null,
                'start_date' => $this->staffForm['start_date'] ?: null,
                'end_date' => $this->staffForm['end_date'] ?: null,
            ]);
            session()->flash('success', __('app.staff_saved'));
        }

        $this->closeModal();
        $this->loadStaff();
    }

    public function deleteStaff($id)
    {
        $record = TeamStaff::findOrFail($id);
        $record->delete();
        session()->flash('success', __('app.staff_deleted'));
        $this->loadStaff();
    }

    public function updatedSearch()
    {
        $this->loadStaff();
    }

    public function render()
    {
        return view('livewire.admin.teams.team-staff-page', [
            'title' => __('app.staff') . ' - ' . $this->team->name,
            'staffRoles' => TeamStaff::STAFF_ROLES,
            'staffIcons' => TeamStaff::STAFF_ICONS,
        ]);
    }
}
