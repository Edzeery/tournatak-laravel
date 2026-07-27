<?php
namespace App\Livewire\Admin\Types;

use App\Models\CompetitionType;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class TypesPage extends Component
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

    public function resetPage()
    {
        $this->setPage(1);
    }

    public function toggleActive($id)
    {
        $type = CompetitionType::findOrFail($id);
        $type->update(['is_active' => !$type->is_active]);
        session()->flash('success', $type->is_active ? 'تم تفعيل النوع بنجاح' : 'تم إلغاء تفعيل النوع بنجاح');
    }

    public function delete($id)
    {
        $type = CompetitionType::findOrFail($id);
        $type->delete();
        session()->flash('success', 'تم حذف النوع بنجاح');
    }

    public function render()
    {
        $query = CompetitionType::query()
            ->with('subtype')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('slug', 'like', "%{$this->search}%"));

        return view('livewire.admin.types.types-page', [
            'title' => 'إدارة الأنواع',
            'types' => $query->orderBy('sort_order')->latest()->paginate($this->perPage),
        ]);
    }
}
