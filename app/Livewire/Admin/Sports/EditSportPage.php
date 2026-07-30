<?php

namespace App\Livewire\Admin\Sports;

use App\Models\Sport;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class EditSportPage extends Component
{
    public Sport $sport;

    public string $name = '';

    public string $slug = '';

    public ?string $name_en = null;

    public ?string $name_fr = null;

    public ?string $name_es = null;

    public string $category = 'team';

    public ?string $icon = null;

    public int $sort_order = 0;

    public bool $is_active = true;

    public function mount(Sport $sport)
    {
        $this->sport = $sport;
        $this->name = $sport->name;
        $this->slug = $sport->slug;
        $this->name_en = $sport->name_en;
        $this->name_fr = $sport->name_fr;
        $this->name_es = $sport->name_es;
        $this->category = $sport->category;
        $this->icon = $sport->icon;
        $this->sort_order = $sport->sort_order;
        $this->is_active = $sport->is_active;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sports,slug,'.$this->sport->id,
            'name_en' => 'nullable|string|max:255',
            'name_fr' => 'nullable|string|max:255',
            'name_es' => 'nullable|string|max:255',
            'category' => 'required|in:team,individual',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        $this->sport->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'name_en' => $this->name_en,
            'name_fr' => $this->name_fr,
            'name_es' => $this->name_es,
            'category' => $this->category,
            'icon' => $this->icon,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', __('app.sport_updated'));

        return redirect()->route('admin.sports.index');
    }

    public function render()
    {
        return view('livewire.admin.sports.edit-sport-page', [
            'title' => __('app.page_title_edit_sport'),
        ]);
    }
}
