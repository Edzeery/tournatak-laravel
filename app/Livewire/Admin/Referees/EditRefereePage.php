<?php

namespace App\Livewire\Admin\Referees;

use App\Models\Referee;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class EditRefereePage extends Component
{
    public Referee $referee;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $specialization = 'referee';

    public string $license_number = '';

    public string $federation = '';

    public string $nationality = '';

    public bool $is_active = true;

    public string $notes = '';

    public function mount(Referee $referee)
    {
        $this->authorize('update', $referee);
        $this->referee = $referee;
        $this->name = $referee->name;
        $this->email = $referee->email ?? '';
        $this->phone = $referee->phone ?? '';
        $this->specialization = $referee->specialization;
        $this->license_number = $referee->license_number ?? '';
        $this->federation = $referee->federation ?? '';
        $this->nationality = $referee->nationality ?? '';
        $this->is_active = $referee->is_active;
        $this->notes = $referee->notes ?? '';
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'specialization' => 'required|in:referee,assistant_referee,fourth_official',
            'license_number' => 'nullable|string|max:255',
            'federation' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $this->referee->update([
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'specialization' => $this->specialization,
            'license_number' => $this->license_number ?: null,
            'federation' => $this->federation ?: null,
            'nationality' => $this->nationality ?: null,
            'is_active' => $this->is_active,
            'notes' => $this->notes ?: null,
        ]);

        session()->flash('success', __('app.referee_updated'));

        return redirect()->route('admin.referees.index');
    }

    public function render()
    {
        return view('livewire.admin.referees.edit-referee-page', [
            'title' => __('app.edit_referee'),
        ]);
    }
}
