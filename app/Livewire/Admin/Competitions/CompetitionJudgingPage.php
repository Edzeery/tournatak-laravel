<?php

namespace App\Livewire\Admin\Competitions;

use App\Livewire\Concerns\Notifies;
use App\Models\Competition;
use App\Models\Judge;
use App\Models\User;
use App\Services\SubmissionScoringEngine;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CompetitionJudgingPage extends Component
{
    use Notifies;

    public Competition $competition;

    public ?int $newJudgeUserId = null;

    public bool $newJudgeLead = false;

    public bool $hideOtherJudges = true;

    public function mount(Competition $competition): void
    {
        $this->authorize('update', $competition);

        $this->competition = $competition;
        $this->hideOtherJudges = $competition->format_config['judging']['hide_other_judges'] ?? true;
    }

    public function addJudge(): void
    {
        $this->authorize('create', [Judge::class, $this->competition]);

        $this->validate([
            'newJudgeUserId' => 'required|exists:users,id',
        ]);

        if ($this->competition->judges()->where('user_id', $this->newJudgeUserId)->exists()) {
            $this->addError('newJudgeUserId', __('app.judge_already_assigned'));

            return;
        }

        $this->competition->judges()->create([
            'user_id' => $this->newJudgeUserId,
            'is_lead' => $this->newJudgeLead,
        ]);

        $this->reset('newJudgeUserId', 'newJudgeLead');

        $this->notify('success', __('app.judge_assigned'));
    }

    public function removeJudge(int $judgeId): void
    {
        $judge = $this->competition->judges()->findOrFail($judgeId);
        $this->authorize('delete', $judge);

        $judge->delete();

        $this->notify('success', __('app.judge_removed'));
    }

    public function saveSettings(): void
    {
        $config = $this->competition->format_config ?? [];
        $config['judging'] = ['hide_other_judges' => $this->hideOtherJudges];
        $this->competition->update(['format_config' => $config]);

        $this->notify('success', __('app.settings_saved'));
    }

    public function render(SubmissionScoringEngine $engine)
    {
        return view('livewire.admin.competitions.competition-judging-page', [
            'title' => __('app.manage_judging'),
            'judges' => $this->competition->judges()->with('user')->get(),
            'ranking' => $engine->calculateRanking($this->competition),
            'maxScore' => $engine->maxScore($this->competition),
            'aggregation' => $engine->getConfig($this->competition)['aggregation'] ?? SubmissionScoringEngine::AGGREGATION_AVERAGE,
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
