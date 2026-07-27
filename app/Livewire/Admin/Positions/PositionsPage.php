<?php

namespace App\Livewire\Admin\Positions;

use App\Models\Position;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class PositionsPage extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingPositionId = null;
    public $search = '';
    public $filterSport = '';

    public $positionForm = [
        'name' => '',
        'name_en' => '',
        'category' => 'player',
        'sport_type' => 'football',
        'abbreviation' => '',
        'sort_order' => 0,
        'is_active' => true,
    ];

    protected $listeners = ['closeModal'];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterSport(): void { $this->resetPage(); }

    public function mount(): void
    {
        //
    }

    #[Computed]
    public function positions()
    {
        return Position::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('name_en', 'like', "%{$this->search}%")
                    ->orWhere('abbreviation', 'like', "%{$this->search}%");
            })
            ->when($this->filterSport, function ($q) {
                $q->where('sport_type', $this->filterSport);
            })
            ->orderBy('sport_type')
            ->orderBy('sort_order')
            ->paginate(20);
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
            'name' => '',
            'name_en' => '',
            'category' => 'player',
            'sport_type' => 'football',
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
            'name' => $pos->name,
            'name_en' => $pos->name_en ?? '',
            'category' => $pos->category,
            'sport_type' => $pos->sport_type,
            'abbreviation' => $pos->abbreviation ?? '',
            'sort_order' => $pos->sort_order,
            'is_active' => $pos->is_active,
        ];
        $this->showModal = true;
    }

    public function savePosition()
    {
        $this->validate([
            'positionForm.name' => 'required|string|max:255',
            'positionForm.name_en' => 'nullable|string|max:255',
            'positionForm.category' => 'required|in:goalkeeper,defender,midfielder,forward,player',
            'positionForm.sport_type' => 'required|in:football,futsal',
            'positionForm.abbreviation' => 'nullable|string|max:10',
            'positionForm.sort_order' => 'required|integer|min:0',
            'positionForm.is_active' => 'boolean',
        ]);

        if ($this->editingPositionId) {
            Position::findOrFail($this->editingPositionId)->update($this->positionForm);
            session()->flash('success', __('app.position_updated'));
        } else {
            Position::create($this->positionForm);
            session()->flash('success', __('app.position_created'));
        }

        $this->closeModal();
    }

    public function deletePosition($id)
    {
        Position::findOrFail($id)->delete();
        session()->flash('success', __('app.position_deleted'));
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
        ]);
    }
}
