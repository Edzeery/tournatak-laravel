<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-calendar-event-fill text-gold"></i> إدارة المباريات</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">عرض وإدارة المباريات والنتائج</p>
        </div>
        <a href="{{ route('admin.matches.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> إضافة مباراة
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">المباريات</li>
        </ol>
    </nav>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <form wire:submit="resetPage" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">بحث</label>
                    <input type="text" class="form-control" placeholder="بحث بالبطولة أو الفريق..." wire:model.live.debounce.300ms="search">
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
                            <th>البطولة</th>
                            <th>المباراة</th>
                            <th>النتيجة</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matches as $match)
                            <tr wire:key="{{ $match->id }}">
                                <td style="color:#94a3b8;">{{ $match->id }}</td>
                                <td>{{ $match->competition->name ?? '-' }}</td>
                                <td class="fw-bold">
                                    {{ $match->team1->name ?? '?' }}
                                    <span class="text-gold mx-1 fw-bold">vs</span>
                                    {{ $match->team2->name ?? '?' }}
                                </td>
                                <td>
                                    <span class="badge-sport" style="font-size:0.9rem;">
                                        {{ $match->score_team1 ?? 0 }} - {{ $match->score_team2 ?? 0 }}
                                    </span>
                                </td>
                                <td style="font-size:0.85rem;color:#94a3b8;">
                                    {{ $match->match_date?->format('Y/m/d H:i') ?? '-' }}
                                </td>
                                <td>
                                    <x-status-badge domain="match" status="{{ $match->status }}" set="bi" />
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.matches.edit', $match) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.matches.lineup', $match->id) }}" class="btn btn-sm btn-outline-success" title="التشكيلة"><i class="bi bi-people-fill"></i></a>
                                    <a href="{{ route('admin.matches.events', $match->id) }}" class="btn btn-sm btn-outline-warning" title="الأحداث"><i class="bi bi-clock-history"></i></a>
                                    <a href="{{ route('admin.matches.stats', $match->id) }}" class="btn btn-sm btn-outline-primary" title="الإحصائيات"><i class="bi bi-bar-chart-line"></i></a>
                                    <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                            wire:click="delete({{ $match->id }})"
                                            wire:confirm="هل أنت متأكد من حذف هذه المباراة؟">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state py-3"><i class="bi bi-calendar-event d-block" style="font-size:2.5rem;"></i><h5>لا توجد مباريات</h5></div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $matches->links() }}</div>
</div>
