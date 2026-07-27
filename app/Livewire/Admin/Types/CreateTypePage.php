<?php
namespace App\Livewire\Admin\Types;

use App\Models\CompetitionType;
use App\Models\CompetitionSubtype;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Str;

#[Layout('layouts.admin')]
class CreateTypePage extends Component
{
    public string $name = '';
    public string $slug = '';
    public ?string $description = null;
    public ?int $subtype_id = null;
    public ?string $icon = null;
    public int $sort_order = 0;

    public function updatedName()
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:competition_types,slug',
            'description' => 'nullable|string',
            'subtype_id' => 'nullable|exists:competition_subtypes,id',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        CompetitionType::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'subtype_id' => $this->subtype_id,
            'icon' => $this->icon,
            'sort_order' => $this->sort_order,
            'is_active' => true,
        ]);

        session()->flash('success', __('app.type_created'));
        return redirect()->route('admin.types.index');
    }

    public function render()
    {
        return view('livewire.admin.types.create-type-page', [
            'title' => __('app.page_title_add_type'),
            'subtypes' => CompetitionSubtype::orderBy('name')->get(),
        ]);
    }
}
