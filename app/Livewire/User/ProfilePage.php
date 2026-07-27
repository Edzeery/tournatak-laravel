<?php

namespace App\Livewire\User;

use App\Models\Profile;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProfilePage extends Component
{
    public $full_name = '';
    public $profile_date_birth = '';
    public $hasProfile = false;

    protected $listeners = ['profileSaved' => '$refresh'];

    public function mount()
    {
        $user = auth()->user();
        if ($user->profile) {
            $this->hasProfile = true;
            $this->full_name = $user->profile->full_name ?? '';
            $this->profile_date_birth = $user->profile->profile_date_birth?->format('Y-m-d') ?? '';
        }
    }

    public function save()
    {
        $user = auth()->user();

        $validated = $this->validate([
            'full_name' => 'required|string|max:255',
            'profile_date_birth' => 'nullable|date',
        ]);

        $data = array_merge($validated, [
            'user_id' => $user->id,
        ]);

        if ($this->hasProfile) {
            $user->profile->update($data);
        } else {
            Profile::create($data);
            $this->hasProfile = true;
        }

        session()->flash('success', __('app.profile_updated'));
    }

    public function render()
    {
        return view('livewire.user.profile-page', [
            'title' => __('app.page_title_profile'),
            'user' => auth()->user(),
        ]);
    }
}
