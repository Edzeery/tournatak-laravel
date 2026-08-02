<?php

namespace App\Livewire\Admin\Competitions;

use App\Livewire\Concerns\Notifies;
use App\Models\Competition;
use App\Models\CompetitionRound;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class RoundsPage extends Component
{
    use Notifies;

    public Competition $competition;

    public string $name = '';

    public ?int $number = null;

    public ?string $starts_at = null;

    public ?string $ends_at = null;

    public function mount(Competition $competition): void
    {
        $this->authorize('update', $competition);

        $this->competition = $competition;
        $this->number = $this->nextRoundNumber();
    }

    private function nextRoundNumber(): int
    {
        return ($this->competition->rounds()->max('number') ?? 0) + 1;
    }

    public function create(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($this->competition->rounds()->where('number', $this->number)->exists()) {
            $this->addError('number', __('app.round_number_exists'));

            return;
        }

        $this->competition->rounds()->create([
            'name' => $this->name,
            'number' => $this->number,
            'status' => CompetitionRound::STATUS_SCHEDULED,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
        ]);

        $this->reset('name', 'starts_at', 'ends_at');
        $this->number = $this->nextRoundNumber();

        $this->notify('success', __('app.round_created'));
    }

    public function render()
    {
        return view('livewire.admin.competitions.rounds-page', [
            'title' => __('app.manage_rounds'),
            'rounds' => $this->competition->rounds()
                ->withCount('submissions')
                ->orderBy('number')
                ->orderBy('id')
                ->get(),
        ]);
    }
}
