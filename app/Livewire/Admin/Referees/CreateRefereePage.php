<?php

namespace App\Livewire\Admin\Referees;

use App\Models\Referee;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateRefereePage extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $specialization = 'referee';

    public string $license_number = '';

    public string $federation = '';

    public string $nationality = '';

    public bool $is_active = true;

    public string $notes = '';

    public function store()
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

        Referee::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'specialization' => $this->specialization,
            'license_number' => $this->license_number,
            'federation' => $this->federation,
            'nationality' => $this->nationality,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
        ]);

        session()->flash('success', __('app.referee_created'));

        return redirect()->route('admin.referees.index');
    }

    public function render()
    {
        return view('livewire.admin.referees.create-referee-page', [
            'title' => __('app.add_referee'),
        ]);
    }
}
