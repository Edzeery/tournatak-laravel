<?php

namespace App\Livewire\Admin\Positions;

use App\Livewire\Concerns\Notifies;
use App\Models\Position;
use App\Models\Sport;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class PositionsPage extends Component
{
    use Notifies;
    use WithPagination;

    public $showModal = false;

    public $editingPositionId = null;

    public $search = '';

    public $filterSport = '';

    public $positionForm = [
        'sport_id' => '',
        'name' => '',
        'name_en' => '',
        'category' => 'player',
        'abbreviation' => '',
        'sort_order' => 0,
        'is_active' => true,
    ];

    protected $listeners = ['closeModal'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterSport(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function positions()
    {
        return Position::query()
            ->with('sport')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('name_en', 'like', "%{$this->search}%")
                    ->orWhere('abbreviation', 'like', "%{$this->search}%");
            })
            ->when($this->filterSport, function ($q) {
                $q->where('sport_id', $this->filterSport);
            })
            ->orderBy('sport_id')
            ->orderBy('sort_order')
            ->paginate(20);
    }

    #[Computed]
    public function sports()
    {
        return Sport::where('is_active', true)->orderBy('sort_order')->get();
    }

    #[Computed]
    public function sportCategories()
    {
        if (! $this->positionForm['sport_id']) {
            return Sport::DEFAULT_POSITION_CATEGORIES;
        }
        $sport = Sport::find($this->positionForm['sport_id']);

        return $sport ? $sport->position_categories : Sport::DEFAULT_POSITION_CATEGORIES;
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingPositionId = null;
        $this->positionForm = [
            'sport_id' => '',
            'name' => '',
            'name_en' => '',
            'category' => 'player',
            'abbreviation' => '',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function editPosition($id)
    {
        $pos = Position::findOrFail($id);
        $this->editingPositionId = $id;
        $this->positionForm = [
            'sport_id' => (string) $pos->sport_id,
            'name' => $pos->name,
            'name_en' => $pos->name_en ?? '',
            'category' => $pos->category,
            'abbreviation' => $pos->abbreviation ?? '',
            'sort_order' => $pos->sort_order,
            'is_active' => $pos->is_active,
        ];
        $this->showModal = true;
    }

    public function savePosition()
    {
        $this->validate([
            'positionForm.sport_id' => 'nullable|exists:sports,id',
            'positionForm.name' => 'required|string|max:255',
            'positionForm.name_en' => 'nullable|string|max:255',
            'positionForm.category' => 'required|string|max:50',
            'positionForm.abbreviation' => 'nullable|string|max:10',
            'positionForm.sort_order' => 'required|integer|min:0',
            'positionForm.is_active' => 'boolean',
        ]);

        if ($this->editingPositionId) {
            Position::findOrFail($this->editingPositionId)->update($this->positionForm);
            Position::bustCache();
            $this->notify('success', __('app.position_updated'));
        } else {
            Position::create($this->positionForm);
            Position::bustCache();
            $this->notify('success', __('app.position_created'));
        }

        $this->closeModal();
    }

    public function deletePosition($id)
    {
        Position::findOrFail($id)->delete();
        Position::bustCache();
        $this->notify('success', __('app.position_deleted'));
    }

    public function updatedSearch()
    {
        //
    }

    public function updatedFilterSport()
    {
        //
    }

    public function render()
    {
        return view('livewire.admin.positions.positions-page', [
            'title' => __('app.page_title_manage_positions'),
            'positions' => $this->positions(),
            'sports' => $this->sports(),
            'sportCategories' => $this->sportCategories(),
        ]);
    }
}
