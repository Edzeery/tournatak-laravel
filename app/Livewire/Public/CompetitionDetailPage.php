<?php

namespace App\Livewire\Public;

use App\Models\Competition;
use App\Models\Match_;
use App\Services\StandingService;
use App\Services\SubmissionScoringEngine;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CompetitionDetailPage extends Component
{
    public Competition $competition;

    public ?int $selectedMatchId = null;

    public $selectedMatch = null;

    public string $selectedDate;

    public string $filterMode = 'date';

    public string $search = '';

    public function mount(Competition $competition): void
    {
        $this->competition = $competition->load([
            'type', 'subtype', 'organizer',
            'teams',
            'matches' => function ($q) {
                $q->with(['team1', 'team2'])
                    ->orderBy('match_date')
                    ->orderBy('id');
            },
        ]);

        $this->selectedDate = $this->nearestMatchDate($this->competition->matches);
    }

    private function nearestMatchDate($matches): string
    {
        $today = today();
        foreach ($matches as $m) {
            if ($m->match_date && $m->match_date->isSameDay($today)) {
                return $today->format('Y-m-d');
            }
        }
        $upcoming = $matches->first(fn ($m) => $m->match_date && $m->match_date->gte($today));
        if ($upcoming) {
            return $upcoming->match_date->format('Y-m-d');
        }

        return $today->format('Y-m-d');
    }

    public function goToDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->filterMode = 'date';
    }

    public function prevDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->format('Y-m-d');
        $this->filterMode = 'date';
    }

    public function nextDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->format('Y-m-d');
        $this->filterMode = 'date';
    }

    public function today(): void
    {
        $this->selectedDate = today()->format('Y-m-d');
        $this->filterMode = 'date';
    }

    public function setFilter(string $mode): void
    {
        $this->filterMode = $mode;
    }

    public function showMatchDetail($matchId): void
    {
        $this->selectedMatchId = $matchId;
        $this->selectedMatch = Match_::with([
            'team1', 'team2', 'competition',
            'events' => function ($q) {
                $q->with(['player.user', 'relatedPlayer.user', 'team'])
                    ->orderBy('minute')
                    ->orderBy('added_time');
            },
            'lineups' => function ($q) {
                $q->with(['player.user', 'position'])
                    ->where('is_starter', true);
            },
            'stats' => function ($q) {
                $q->with('team');
            },
        ])->findOrFail($matchId);
    }

    public function closeMatchDetail(): void
    {
        $this->selectedMatchId = null;
        $this->selectedMatch = null;
    }

    public function render(StandingService $standingService)
    {
        if ($this->competition->evaluationBasis() === 'submission') {
            return $this->renderSubmissionDetail();
        }

        $this->competition->load([
            'teams',
            'matches' => function ($q) {
                $q->with(['team1', 'team2'])
                    ->orderBy('match_date')
                    ->orderBy('id');
            },
        ]);

        $standings = [];
        if (in_array($this->competition->format, ['league', 'home_away', 'groups'])) {
            $standings = $standingService->calculate($this->competition);
        }

        $allDates = $this->competition->matches
            ->map(fn ($m) => $m->match_date?->format('Y-m-d'))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $center = Carbon::parse($this->selectedDate);
        $dateRange = [];
        for ($i = -3; $i <= 3; $i++) {
            $date = (clone $center)->addDays($i);
            $dateStr = $date->format('Y-m-d');
            $dateRange[] = [
                'date' => $dateStr,
                'day' => (int) $date->format('j'),
                'month' => $date->format('M'),
                'dow' => $date->isoFormat('dd'),
                'isToday' => $dateStr === today()->format('Y-m-d'),
                'isSelected' => $dateStr === $this->selectedDate,
                'hasMatches' => in_array($dateStr, $allDates),
            ];
        }

        $allColl = $this->competition->matches;

        if ($this->filterMode === 'live') {
            $filteredMatches = $allColl->filter(fn ($m) => $m->status === 'in_progress');
        } elseif ($this->filterMode === 'all') {
            $filteredMatches = $allColl;
        } else {
            $filteredMatches = $allColl->filter(
                fn ($m) => $m->match_date?->format('Y-m-d') === $this->selectedDate
            );
        }

        if ($this->search !== '') {
            $filteredMatches = $filteredMatches->filter(function ($m) {
                $q = strtolower($this->search);

                return str_contains(strtolower($m->team1?->name ?? ''), $q)
                    || str_contains(strtolower($m->team2?->name ?? ''), $q);
            });
        }

        $filteredMatches = $filteredMatches->values();

        return view('livewire.public.competition-detail-page', [
            'title' => $this->competition->name,
            'standings' => $standings,
            'topScorers' => $standingService->getTopScorers($this->competition, 5),
            'dateRange' => $dateRange,
            'filteredMatches' => $filteredMatches,
        ]);
    }

    private function renderSubmissionDetail()
    {
        $engine = app(SubmissionScoringEngine::class);
        $this->competition->load([
            'type', 'subtype', 'organizer', 'domain',
            'teams',
            'rounds' => fn ($q) => $q->orderBy('number')->orderBy('id'),
            'submissions' => fn ($q) => $q->with(['round', 'team', 'user', 'player'])
                ->orderByDesc('id'),
            'judges' => fn ($q) => $q->with('user'),
        ]);

        return view('livewire.public.submission-competition-detail-page', [
            'title' => $this->competition->name,
            'ranking' => $engine->calculateRanking($this->competition),
            'maxScore' => $engine->maxScore($this->competition),
            'aggregation' => $engine->getConfig($this->competition)['aggregation'] ?? SubmissionScoringEngine::AGGREGATION_AVERAGE,
        ]);
    }
}
