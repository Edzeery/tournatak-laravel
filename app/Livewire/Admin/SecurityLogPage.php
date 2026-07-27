<?php

namespace App\Livewire\Admin;

use App\Models\Activity;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class SecurityLogPage extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $filterUser = null;
    public ?string $filterEvent = null;

    protected $queryString = ['search', 'filterUser', 'filterEvent'];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterUser(): void { $this->resetPage(); }
    public function updatingFilterEvent(): void { $this->resetPage(); }

    public function getRecords()
    {
        $query = Activity::with('user')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('event', 'like', "%{$this->search}%")
                  ->orWhere('type', 'like', "%{$this->search}%")
                  ->orWhere('ip_address', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }

        if ($this->filterEvent) {
            $query->where('event', $this->filterEvent);
        }

        return $query->paginate(20);
    }

    public function render()
    {
        return view('livewire.admin.security-log-page', [
            'records' => $this->getRecords(),
            'users' => User::orderBy('name')->pluck('name', 'id'),
            'eventTypes' => Activity::distinct()->pluck('event')->filter()->sort()->values(),
            'title' => 'سجل الأمان',
        ]);
    }
}
