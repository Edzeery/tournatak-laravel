<?php
namespace App\Livewire\Admin\Competitions;

use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\CompetitionSubtype;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateCompetitionPage extends Component
{
    public string $name = '';
    public ?int $type_id = null;
    public ?int $subtype_id = null;
    public ?string $location = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public ?string $description = null;

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:competition_types,id',
            'subtype_id' => 'required|exists:competition_subtypes,id',
        ]);

        Competition::create([
            'name' => $this->name,
            'type_id' => $this->type_id,
            'subtype_id' => $this->subtype_id,
            'organizer_id' => auth()->id(),
            'location' => $this->location,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'description' => $this->description,
            'approval_status' => 'pending',
            'status' => 'draft',
        ]);

        session()->flash('success', 'تم إنشاء البطولة بنجاح');
        return redirect()->route('admin.competitions.index');
    }

    public function render()
    {
        return view('livewire.admin.competitions.create-competition-page', [
            'title' => 'إضافة بطولة',
            'types' => CompetitionType::where('is_active', true)->get(),
            'subtypes' => CompetitionSubtype::all(),
        ]);
    }
}
