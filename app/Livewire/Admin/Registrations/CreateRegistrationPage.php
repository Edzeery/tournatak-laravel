<?php

namespace App\Livewire\Admin\Registrations;

use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\Registration;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateRegistrationPage extends Component
{
    public ?int $competition_id = null;

    public ?int $user_id = null;

    public string $searchUser = '';

    public function store()
    {
        $this->validate([
            'competition_id' => 'required|exists:competitions,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $competition = Competition::findOrFail($this->competition_id);

        $existing = Registration::where('competition_id', $this->competition_id)
            ->where('participant_type', Registration::PARTICIPANT_INDIVIDUAL)
            ->where('user_id', $this->user_id)
            ->first();

        if ($existing) {
            session()->flash('error', __('app.registration_already_exists'));

            return;
        }

        Registration::create([
            'competition_id' => $this->competition_id,
            'participant_type' => Registration::PARTICIPANT_INDIVIDUAL,
            'user_id' => $this->user_id,
            'status' => Registration::STATUS_APPROVED,
        ]);

        session()->flash('success', __('app.individual_registration_created'));

        return redirect()->route('admin.registrations.index');
    }

    public function render()
    {
        $individualTypeIds = CompetitionType::whereIn('participant_type', ['individual', 'both'])->pluck('id');

        $competitions = Competition::whereIn('type_id', $individualTypeIds)
            ->where('approval_status', 'approved')
            ->latest()
            ->get();

        $users = User::query()
            ->when($this->searchUser, fn ($q) => $q->where('name', 'like', "%{$this->searchUser}%")
                ->orWhere('email', 'like', "%{$this->searchUser}%"))
            ->orderBy('name')
            ->limit(20)
            ->get();

        return view('livewire.admin.registrations.create-registration-page', [
            'title' => __('app.page_title_add_registration'),
            'competitions' => $competitions,
            'users' => $users,
        ]);
    }
}
