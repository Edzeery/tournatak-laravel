<?php
namespace App\Livewire\Admin\Subtypes;

use App\Models\CompetitionSubtype;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class SubtypesPage extends Component
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

    public function delete($id)
    {
        $subtype = CompetitionSubtype::findOrFail($id);
        $subtype->delete();
        session()->flash('success', __('app.subtype_deleted'));
    }

    public function render()
    {
        $query = CompetitionSubtype::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('en_name', 'like', "%{$this->search}%"));

        return view('livewire.admin.subtypes.subtypes-page', [
            'title' => __('app.page_title_manage_subtypes'),
            'subtypes' => $query->latest()->paginate($this->perPage),
        ]);
    }
}
