<?php

namespace App\Livewire\Public;

use App\Models\Match_;
use App\Models\Competition;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MatchesPage extends Component
{
    use WithPagination;

    public string $selectedDate = '';
    public string $filterMode = 'date';
    public string $statusFilter = '';
    public string $search = '';
    public ?int $competitionId = null;

    public function mount(): void
    {
        $this->selectedDate = $this->nearestMatchDate();
    }

    private function nearestMatchDate(): string
    {
        $today = today()->format('Y-m-d');
        if (Match_::whereDate('match_date', $today)->exists()) {
            return $today;
        }
        $next = Match_::whereDate('match_date', '>=', $today)
            ->orderBy('match_date')
            ->first();
        return $next ? $next->match_date->format('Y-m-d') : $today;
    }

    public function goToDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->filterMode = 'date';
        $this->resetPage();
    }

    public function prevDay(): void
    {
        $this->selectedDate = \Carbon\Carbon::parse($this->selectedDate)->subDay()->format('Y-m-d');
        $this->filterMode = 'date';
        $this->resetPage();
    }

    public function nextDay(): void
    {
        $this->selectedDate = \Carbon\Carbon::parse($this->selectedDate)->addDay()->format('Y-m-d');
        $this->filterMode = 'date';
        $this->resetPage();
    }

    public function today(): void
    {
        $this->selectedDate = today()->format('Y-m-d');
        $this->filterMode = 'date';
        $this->resetPage();
    }

    public function setFilter(string $mode): void
    {
        $this->filterMode = $mode;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCompetitionId(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Match_::with(['team1', 'team2', 'competition'])
            ->orderBy('match_date')
            ->orderBy('id');

        if ($this->filterMode === 'live') {
            $query->where('status', 'in_progress');
        } elseif ($this->filterMode === 'date' && $this->selectedDate) {
            $query->whereDate('match_date', $this->selectedDate);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->competitionId) {
            $query->where('competition_id', $this->competitionId);
        }

        if ($this->search) {
            $q = $this->search;
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('team1', fn($t) => $t->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('team2', fn($t) => $t->where('name', 'like', "%{$q}%"));
            });
        }

        $matches = $query->paginate(20);

        $todayStr = today()->format('Y-m-d');
        $center = \Carbon\Carbon::parse($this->selectedDate);
        $dateRange = [];
        for ($i = -3; $i <= 3; $i++) {
            $d = (clone $center)->addDays($i);
            $dateRange[] = [
                'date' => $d->format('Y-m-d'),
                'day' => (int) $d->format('j'),
                'month' => $d->format('M'),
                'dow' => $d->isoFormat('dd'),
                'isToday' => $d->format('Y-m-d') === $todayStr,
                'isSelected' => $d->format('Y-m-d') === $this->selectedDate,
            ];
        }

        $competitions = Competition::where('approval_status', 'approved')
            ->whereHas('matches')
            ->orderBy('name')
            ->get();

        return view('livewire.public.matches-page', [
            'title' => __('app.page_title_matches'),
            'matches' => $matches,
            'dateRange' => $dateRange,
            'competitions' => $competitions,
        ]);
    }
}
