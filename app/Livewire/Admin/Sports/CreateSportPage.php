<?php

namespace App\Livewire\Admin\Sports;

use App\Models\Sport;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateSportPage extends Component
{
    public string $name = '';

    public string $slug = '';

    public ?string $name_en = null;

    public ?string $name_fr = null;

    public ?string $name_es = null;

    public string $category = 'team';

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
            'slug' => 'required|string|max:255|unique:sports,slug',
            'name_en' => 'nullable|string|max:255',
            'name_fr' => 'nullable|string|max:255',
            'name_es' => 'nullable|string|max:255',
            'category' => 'required|in:team,individual',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        Sport::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'name_en' => $this->name_en,
            'name_fr' => $this->name_fr,
            'name_es' => $this->name_es,
            'category' => $this->category,
            'icon' => $this->icon,
            'sort_order' => $this->sort_order,
            'is_active' => true,
        ]);

        session()->flash('success', __('app.sport_created'));

        return redirect()->route('admin.sports.index');
    }

    public function render()
    {
        return view('livewire.admin.sports.create-sport-page', [
            'title' => __('app.page_title_add_sport'),
        ]);
    }
}
