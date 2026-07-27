<?php
namespace App\Livewire\Admin\Types;

use App\Models\CompetitionType;
use App\Models\CompetitionSubtype;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class EditTypePage extends Component
{
    public CompetitionType $type;
    public string $name = '';
    public string $slug = '';
    public ?string $description = null;
    public ?int $subtype_id = null;
    public ?string $icon = null;
    public int $sort_order = 0;
    public bool $is_active = true;

    public function mount(CompetitionType $type)
    {
        $this->type = $type;
        $this->name = $type->name;
        $this->slug = $type->slug;
        $this->description = $type->description;
        $this->subtype_id = $type->subtype_id;
        $this->icon = $type->icon;
        $this->sort_order = $type->sort_order;
        $this->is_active = $type->is_active;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:competition_types,slug,' . $this->type->id,
            'description' => 'nullable|string',
            'subtype_id' => 'nullable|exists:competition_subtypes,id',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        $this->type->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'subtype_id' => $this->subtype_id,
            'icon' => $this->icon,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', __('app.type_updated'));
        return redirect()->route('admin.types.index');
    }

    public function render()
    {
        return view('livewire.admin.types.edit-type-page', [
            'title' => __('app.page_title_edit_type'),
            'type' => $this->type,
            'subtypes' => CompetitionSubtype::orderBy('name')->get(),
        ]);
    }
}
