<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-people-fill text-gold"></i> إدارة
                المستخدمين</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">عرض وإدارة حسابات المستخدمين</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> إضافة مستخدم
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none"
                    style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">المستخدمون</li>
        </ol>
    </nav>

    {{-- Search & Filters --}}
    <div class="card border-0 mb-4">
        <div class="card-body">
            <form wire:submit="resetPage" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">بحث</label>
                    <input type="text" class="form-control" placeholder="بحث بالاسم أو البريد..."
                        wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">الدور</label>
                    <select class="form-select" wire:model.live="roleFilter">
                        <option value="">كل الأدوار</option>
                        <option value="admin">مدير</option>
                        <option value="organizer">منظم</option>
                        <option value="captain">قائد</option>
                        <option value="player">لاعب</option>
                        <option value="competitor">مشارك</option>
                        <option value="viewer">مشاهد</option>
                        <option value="user">مستخدم</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">العرض</label>
                    <select class="form-select" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div wire:loading>
        <x-skeleton type="table" :rows="5" />
    </div>

    {{-- Users Table --}}
    <div class="card border-0" wire:loading.remove>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستخدم</th>
                            <th>البريد</th>
                            <th>الدور</th>
                            <th>التحقق</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr wire:key="{{ $user->id }}">
                                <td style="color:#94a3b8;">{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold"
                                            style="width:38px;height:38px;font-size:0.85rem;">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <div class="d-flex">
                                                <small class="ms-3" style="color:#89929e;"> {{ __('attributes.username') }}
                                                    :
                                                </small> <small class="ms-3"
                                                    style="color:#94a3b8;">{{ $user->username }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td><x-status-badge domain="role" status="{{ $user->role }}" set="bi" /></td>
                                <td>
                                    @if ($user->is_verified)
                                        <x-status-badge domain="user" status="active" set="bi" />
                                    @else
                                        <x-status-badge domain="user" status="email_unverified" set="bi" />
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                        wire:click="delete({{ $user->id }})"
                                        wire:confirm="هل أنت متأكد من حذف هذا المستخدم؟">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr wire:loading.remove>
                                <td colspan="6">
                                    <x-empty-state icon="bi-people-fill" title="{{ __('No Users Found') }}" message="{{ __('No results found. Start by adding a new item.') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
</div>
