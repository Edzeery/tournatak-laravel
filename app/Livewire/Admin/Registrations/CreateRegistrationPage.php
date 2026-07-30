<?php

namespace App\Livewire\Admin\Registrations;

use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\User;
use App\Services\RegistrationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateRegistrationPage extends Component
{
    public ?int $competition_id = null;

    public ?int $user_id = null;

    public string $searchUser = '';

    public function store(RegistrationService $service)
    {
        $this->validate([
            'competition_id' => 'required|exists:competitions,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $result = $service->registerIndividual($this->competition_id, $this->user_id);

        if (! $result['success']) {
            session()->flash('error', $result['message']);

            return;
        }

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
