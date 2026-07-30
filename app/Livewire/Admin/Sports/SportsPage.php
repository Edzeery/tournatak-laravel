<?php

namespace App\Livewire\Admin\Sports;

use App\Models\Sport;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class SportsPage extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function toggleActive($id)
    {
        $sport = Sport::findOrFail($id);
        $sport->update(['is_active' => ! $sport->is_active]);
        session()->flash('success', $sport->is_active ? __('app.sport_toggled_active') : __('app.sport_toggled_inactive'));
    }

    public function delete($id)
    {
        $sport = Sport::findOrFail($id);
        $sport->delete();
        session()->flash('success', __('app.sport_deleted'));
    }

    public function render()
    {
        $query = Sport::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('slug', 'like', "%{$this->search}%")
                ->orWhere('name_en', 'like', "%{$this->search}%"));

        return view('livewire.admin.sports.sports-page', [
            'title' => __('app.page_title_manage_sports'),
            'sports' => $query->orderBy('sort_order')->paginate($this->perPage),
        ]);
    }
}
