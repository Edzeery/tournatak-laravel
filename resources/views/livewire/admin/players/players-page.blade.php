<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-person-badge-fill text-gold"></i> إدارة اللاعبين</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">عرض وإدارة اللاعبين المسجلين</p>
        </div>
        <a href="{{ route('admin.players.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> إضافة لاعب
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">اللاعبون</li>
        </ol>
    </nav>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <form wire:submit="resetPage" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">بحث</label>
                    <input type="text" class="form-control" placeholder="بحث بالاسم أو الفريق..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
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

    <div class="card border-0" wire:loading.opacity>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>الفريق</th>
                            <th>الرقم</th>
                            <th>المركز</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($players as $player)
                            <tr wire:key="{{ $player->id }}">
                                <td style="color:#94a3b8;">{{ $player->id }}</td>
                                <td>
                                    @if($player->image)
                                        <img src="{{ $player->image }}" alt="" class="rounded-circle" style="width:38px;height:38px;object-fit:cover;border:2px solid var(--primary);">
                                    @else
                                        <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:38px;height:38px;font-size:0.85rem;">
                                            {{ mb_substr($player->user->name ?? '?', 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $player->user->name ?? '-' }}</td>
                                <td>{{ $player->team->name ?? '-' }}</td>
                                <td><span class="badge-sport">{{ $player->number ?? '-' }}</span></td>
                                <td>{{ $player->position->name ?? $player->position_text ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.players.edit', $player) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                            wire:click="delete({{ $player->id }})"
                                            wire:confirm="هل أنت متأكد من حذف هذا اللاعب؟">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state py-3"><i class="bi bi-person d-block" style="font-size:2.5rem;"></i><h5>لا يوجد لاعبين</h5></div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $players->links() }}</div>
</div>
