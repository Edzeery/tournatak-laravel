<?php

namespace App\Livewire\Admin\Subtypes;

use App\Models\CompetitionSubtype;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class EditSubtypePage extends Component
{
    public CompetitionSubtype $subtype;

    public string $name = '';

    public string $en_name = '';

    public function mount(CompetitionSubtype $subtype)
    {
        $this->subtype = $subtype;
        $this->name = $subtype->name;
        $this->en_name = $subtype->en_name;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'en_name' => 'required|string|max:255',
        ]);

        $this->subtype->update([
            'name' => $this->name,
            'en_name' => $this->en_name,
        ]);

        session()->flash('success', __('app.subtype_updated'));

        return redirect()->route('admin.subtypes.index');
    }

    public function render()
    {
        return view('livewire.admin.subtypes.edit-subtype-page', [
            'title' => __('app.page_title_edit_subtype'),
            'subtype' => $this->subtype,
        ]);
    }
}
