<?php

namespace App\Livewire\Admin\Referees;

use App\Livewire\Concerns\Notifies;
use App\Models\Referee;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class RefereesPage extends Component
{
    use Notifies;
    use WithPagination;

    public string $search = '';

    public string $specializationFilter = '';

    public int $perPage = 10;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSpecializationFilter()
    {
        $this->resetPage();
    }

    public function toggleActive($id)
    {
        $referee = Referee::findOrFail($id);
        $referee->update(['is_active' => ! $referee->is_active]);
        $this->notify('success', __('app.referee_updated'));
    }

    public function render()
    {
        $query = Referee::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%"))
            ->when($this->specializationFilter, fn ($q) => $q->where('specialization', $this->specializationFilter));

        return view('livewire.admin.referees.referees-page', [
            'title' => __('app.referees'),
            'referees' => $query->latest()->paginate($this->perPage),
        ]);
    }
}
