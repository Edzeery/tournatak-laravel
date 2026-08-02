<?php

namespace App\Livewire\Admin\Types;

use App\Livewire\Concerns\Notifies;
use App\Models\CompetitionType;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class TypesPage extends Component
{
    use Notifies;
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

    public function resetPage()
    {
        $this->setPage(1);
    }

    public function toggleActive($id)
    {
        $type = CompetitionType::findOrFail($id);
        $type->update(['is_active' => ! $type->is_active]);
        $this->notify('success', $type->is_active ? __('app.type_toggled_active') : __('app.type_toggled_inactive'));
    }

    public function delete($id)
    {
        $type = CompetitionType::findOrFail($id);
        $type->delete();
        $this->notify('success', __('app.type_deleted'));
    }

    public function render()
    {
        $query = CompetitionType::query()
            ->with('subtype')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('slug', 'like', "%{$this->search}%"));

        return view('livewire.admin.types.types-page', [
            'title' => __('app.page_title_manage_types'),
            'types' => $query->orderBy('sort_order')->latest()->paginate($this->perPage),
        ]);
    }
}
