<?php
namespace App\Livewire\Admin\Subtypes;

use App\Models\CompetitionSubtype;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateSubtypePage extends Component
{
    public string $name = '';
    public string $en_name = '';

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'en_name' => 'required|string|max:255',
        ]);

        CompetitionSubtype::create([
            'name' => $this->name,
            'en_name' => $this->en_name,
        ]);

        session()->flash('success', 'تم إنشاء النوع الفرعي بنجاح');
        return redirect()->route('admin.subtypes.index');
    }

    public function render()
    {
        return view('livewire.admin.subtypes.create-subtype-page', [
            'title' => 'إضافة تصنيف',
        ]);
    }
}
