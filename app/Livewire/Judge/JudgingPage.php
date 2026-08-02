<?php

namespace App\Livewire\Judge;

use App\Livewire\Concerns\Notifies;
use App\Models\Competition;
use App\Models\Judge;
use App\Models\JudgeScore;
use App\Models\Submission;
use App\Services\SubmissionScoringEngine;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class JudgingPage extends Component
{
    use Notifies;

    public Competition $competition;

    public ?int $round_id = null;

    /**
     * @var array<int, array{score: float|int|string|null, notes: string|null}>
     */
    public array $scores = [];

    public function mount(Competition $competition): void
    {
        $this->authorize('judge', $competition);

        $this->competition = $competition;
        $this->round_id = $this->currentRoundId();
    }

    public function selectRound(int $roundId): void
    {
        $this->round_id = $roundId;
        $this->scores = [];
    }

    private function currentRoundId(): ?int
    {
        return $this->competition->rounds()
            ->orderByRaw("CASE WHEN status = 'in_progress' THEN 0 ELSE 1 END, number ASC")
            ->value('id');
    }

    public function saveScore(int $submissionId): void
    {
        $submission = Submission::with('competition')->findOrFail($submissionId);

        if ($submission->competition_id !== $this->competition->id) {
            abort(403);
        }

        $judge = Judge::firstOrCreate([
            'competition_id' => $this->competition->id,
            'user_id' => auth()->id(),
        ]);

        $score = JudgeScore::firstOrNew([
            'submission_id' => $submissionId,
            'judge_id' => $judge->id,
        ]);

        $this->authorize('update', $score);

        $max = app(SubmissionScoringEngine::class)->maxScore($this->competition);

        $this->validate([
            "scores.{$submissionId}.score" => "required|numeric|min:0|max:{$max}",
        ]);

        $score->score = (float) $this->scores[$submissionId]['score'];
        $score->notes = $this->scores[$submissionId]['notes'] ?? null;
        $score->save();

        $this->notify('success', __('app.score_saved'));
    }

    public function render(SubmissionScoringEngine $engine)
    {
        $rounds = $this->competition->rounds()->orderBy('number')->orderBy('id')->get();
        $round = $rounds->firstWhere('id', $this->round_id);

        $judge = Judge::where('competition_id', $this->competition->id)
            ->where('user_id', auth()->id())
            ->first();

        $submissions = collect();

        if ($round) {
            $submissions = $round->submissions()
                ->with(['team', 'user', 'player', 'judgeScores'])
                ->orderByDesc('id')
                ->get()
                ->map(function (Submission $submission) use ($judge) {
                    $myScore = $submission->judgeScores->where('judge_id', $judge?->id)->first();
                    $average = $submission->judgeScores->avg('score');

                    if (! isset($this->scores[$submission->id])) {
                        $this->scores[$submission->id] = [
                            'score' => $myScore?->score,
                            'notes' => $myScore?->notes,
                        ];
                    }

                    return (object) [
                        'submission' => $submission,
                        'average' => $average ? round((float) $average, 2) : null,
                    ];
                });
        }

        return view('livewire.judge.judging-page', [
            'title' => __('app.judge_competition'),
            'rounds' => $rounds,
            'submissions' => $submissions,
            'hideOtherJudges' => $this->competition->format_config['judging']['hide_other_judges'] ?? true,
            'maxScore' => $engine->maxScore($this->competition),
        ]);
    }
}
