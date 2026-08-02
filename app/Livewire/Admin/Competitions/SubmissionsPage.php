<?php

namespace App\Livewire\Admin\Competitions;

use App\Enums\SubmissionStatus;
use App\Livewire\Concerns\Notifies;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class SubmissionsPage extends Component
{
    use Notifies;
    use WithPagination;

    public Competition $competition;

    public ?int $round_id = null;

    public ?int $editSubmissionId = null;

    public ?int $newRoundId = null;

    public string $newParticipantType = Submission::PARTICIPANT_TEAM;

    public ?int $newParticipantId = null;

    public string $newTitle = '';

    public ?string $newDescription = null;

    public ?int $editRoundId = null;

    public string $editTitle = '';

    public ?string $editDescription = null;

    public string $editStatus = '';

    public function mount(Competition $competition): void
    {
        $this->authorize('update', $competition);

        $this->competition = $competition;
        $this->round_id = request()->integer('round') ?: null;
        $this->newRoundId = $competition->rounds()->orderBy('number')->value('id');
    }

    public function create(): void
    {
        $this->validate([
            'newTitle' => 'required|string|max:255',
            'newDescription' => 'nullable|string',
            'newRoundId' => 'required|exists:competition_rounds,id',
            'newParticipantType' => 'required|in:team,individual',
            'newParticipantId' => 'required|integer',
        ]);

        $data = [
            'competition_id' => $this->competition->id,
            'round_id' => $this->newRoundId,
            'participant_type' => $this->newParticipantType,
            'title' => $this->newTitle,
            'description' => $this->newDescription,
            'status' => SubmissionStatus::Pending->value,
        ];

        if ($this->newParticipantType === Submission::PARTICIPANT_TEAM) {
            $data['team_id'] = Team::findOrFail($this->newParticipantId)->id;
        } else {
            $data['user_id'] = User::findOrFail($this->newParticipantId)->id;
        }

        $this->competition->submissions()->create($data);

        $this->reset('newTitle', 'newDescription', 'newParticipantId');

        $this->notify('success', __('app.submission_created'));
    }

    public function startEdit(int $submissionId): void
    {
        $submission = $this->competition->submissions()->findOrFail($submissionId);

        $this->editSubmissionId = $submission->id;
        $this->editRoundId = $submission->round_id;
        $this->editTitle = $submission->title;
        $this->editDescription = $submission->description;
        $this->editStatus = $submission->status->value;
    }

    public function cancelEdit(): void
    {
        $this->editSubmissionId = null;
    }

    public function update(): void
    {
        $this->validate([
            'editTitle' => 'required|string|max:255',
            'editDescription' => 'nullable|string',
            'editRoundId' => 'required|exists:competition_rounds,id',
            'editStatus' => 'required|in:'.implode(',', array_column(SubmissionStatus::cases(), 'value')),
        ]);

        $submission = $this->competition->submissions()->findOrFail($this->editSubmissionId);
        $this->authorize('update', $submission);

        $submission->update([
            'round_id' => $this->editRoundId,
            'title' => $this->editTitle,
            'description' => $this->editDescription,
            'status' => $this->editStatus,
        ]);

        $this->editSubmissionId = null;

        $this->notify('success', __('app.submission_updated'));
    }

    public function setStatus(int $submissionId, string $status): void
    {
        $submission = $this->competition->submissions()->findOrFail($submissionId);
        $this->authorize('update', $submission);

        if (! in_array($status, array_column(SubmissionStatus::cases(), 'value'))) {
            abort(422);
        }

        $submission->update(['status' => $status]);

        $this->notify('success', __('app.status_updated'));
    }

    public function render()
    {
        $submissions = $this->competition->submissions()
            ->with(['round', 'team', 'user', 'player'])
            ->when($this->round_id, fn ($q) => $q->where('round_id', $this->round_id))
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.competitions.submissions-page', [
            'title' => __('app.manage_submissions'),
            'submissions' => $submissions,
            'rounds' => $this->competition->rounds()->orderBy('number')->orderBy('id')->get(),
            'teamOptions' => $this->competition->teams()->orderBy('name')->get(),
            'individualOptions' => $this->competition->registrations()
                ->where('participant_type', Submission::PARTICIPANT_INDIVIDUAL)
                ->with('user')
                ->get(),
            'statuses' => SubmissionStatus::cases(),
        ]);
    }
}
