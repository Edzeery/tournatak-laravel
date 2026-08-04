<?php

namespace App\Livewire\Admin\Registrations;

use App\Events\RegistrationStatusChanged;
use App\Livewire\Concerns\Notifies;
use App\Models\Registration;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class RegistrationsPage extends Component
{
    use Notifies;
    use WithPagination;

    public string $search = '';

    public string $participantTypeFilter = '';

    public string $statusFilter = '';

    public int $perPage = 10;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedParticipantTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetPage()
    {
        $this->setPage(1);
    }

    public function approve($id)
    {
        $registration = Registration::findOrFail($id);
        $oldStatus = $registration->status;
        $registration->update(['status' => Registration::STATUS_APPROVED]);
        event(new RegistrationStatusChanged($registration, $oldStatus, Registration::STATUS_APPROVED));
        $this->notify('success', __('app.registration_approved'));
    }

    public function reject($id)
    {
        $registration = Registration::findOrFail($id);
        $oldStatus = $registration->status;
        $registration->update(['status' => Registration::STATUS_REJECTED]);
        event(new RegistrationStatusChanged($registration, $oldStatus, Registration::STATUS_REJECTED));
        $this->notify('success', __('app.registration_rejected'));
    }

    public function delete($id)
    {
        $registration = Registration::findOrFail($id);
        $registration->delete();
        $this->notify('success', __('app.registration_deleted'));
    }

    public function render()
    {
        $query = Registration::query()
            ->with(['competition', 'team', 'user', 'player'])
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->whereHas('competition', fn ($cq) => $cq->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('team', fn ($tq) => $tq->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('player', fn ($pq) => $pq->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->participantTypeFilter, fn ($q) => $q->where('participant_type', $this->participantTypeFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        return view('livewire.admin.registrations.registrations-page', [
            'title' => __('app.page_title_manage_registrations'),
            'registrations' => $query->latest()->paginate($this->perPage),
        ]);
    }
}
