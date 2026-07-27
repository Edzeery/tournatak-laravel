<?php
namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class UsersPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public int $perPage = 10;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRoleFilter()
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

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        session()->flash('success', __('app.user_deleted'));
    }

    public function render()
    {
        $query = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('username', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter));

        return view('livewire.admin.users.users-page', [
            'title' => __('app.page_title_manage_users'),
            'users' => $query->latest()->paginate($this->perPage),
        ]);
    }
}
