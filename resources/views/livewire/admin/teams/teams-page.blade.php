<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-shield-fill text-gold"></i> إدارة الفرق</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">عرض وإدارة الفرق المسجلة</p>
        </div>
        <a href="{{ route('admin.teams.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> إضافة فريق
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">الفرق</li>
        </ol>
    </nav>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <form wire:submit="resetPage" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">بحث</label>
                    <input type="text" class="form-control" placeholder="بحث باسم الفريق..." wire:model.live.debounce.300ms="search">
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
                            <th>الشعار</th>
                            <th>اسم الفريق</th>
                            <th>القائد</th>
                            <th>النقاط</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teams as $team)
                            <tr wire:key="{{ $team->id }}">
                                <td style="color:#94a3b8;">{{ $team->id }}</td>
                                <td>
                                    @if($team->logo)
                                        <img src="{{ $team->logo }}" alt="{{ $team->name }}" class="rounded-circle" style="width:38px;height:38px;object-fit:cover;border:2px solid var(--primary);">
                                    @else
                                        <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:38px;height:38px;font-size:0.85rem;">
                                            {{ mb_substr($team->name, 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $team->name }}</td>
                                <td>{{ $team->captain->name ?? '-' }}</td>
                                <td><span class="badge-sport"><i class="bi bi-star-fill"></i> {{ $team->points }}</span></td>
                                <td class="text-center">
                                    <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.teams.staff', $team->id) }}" class="btn btn-sm btn-outline-info" title="الطاقم"><i class="bi bi-people"></i></a>
                                    <a href="{{ route('admin.teams.formations', $team->id) }}" class="btn btn-sm btn-outline-success" title="التشكيلات"><i class="bi bi-grid-3x3-gap"></i></a>
                                    <a href="{{ route('admin.teams.tactics', $team->id) }}" class="btn btn-sm btn-outline-warning" title="التكتيكات"><i class="bi bi-diagram-3"></i></a>
                                    <a href="{{ route('admin.teams.medical', $team->id) }}" class="btn btn-sm btn-outline-danger" title="السجل الطبي"><i class="bi bi-heart-pulse"></i></a>
                                    <a href="{{ route('admin.teams.stats', $team->id) }}" class="btn btn-sm btn-outline-primary" title="الإحصائيات"><i class="bi bi-bar-chart"></i></a>
                                    <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                            wire:click="delete({{ $team->id }})"
                                            wire:confirm="هل أنت متأكد من حذف هذا الفريق؟">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state py-3"><i class="bi bi-shield d-block" style="font-size:2.5rem;"></i><h5>لا توجد فرق</h5></div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $teams->links() }}</div>
</div>
