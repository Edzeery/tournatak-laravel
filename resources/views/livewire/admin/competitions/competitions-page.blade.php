<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-trophy-fill text-gold"></i> إدارة البطولات</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">عرض وإدارة جميع البطولات المسجلة</p>
        </div>
        <a href="{{ route('admin.competitions.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> إضافة بطولة
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">البطولات</li>
        </ol>
    </nav>

    <div class="card border-0" wire:loading.opacity>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>البطولة</th>
                            <th>النوع</th>
                            <th>المنظم</th>
                            <th>التواريخ</th>
                            <th>الحالة</th>
                            <th>الموافقة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($competitions as $competition)
                            <tr wire:key="{{ $competition->id }}">
                                <td style="color:#94a3b8;">{{ $competition->id }}</td>
                                <td class="fw-bold">{{ $competition->name }}</td>
                                <td>
                                    <x-status-badge domain="competition" status="{{ $competition->status }}" set="bi" />
                                </td>
                                <td>{{ $competition->organizer->name ?? '-' }}</td>
                                <td style="font-size:0.85rem;">
                                    {{ $competition->start_date?->format('Y/m/d') ?? '-' }}
                                    <i class="bi bi-arrow-left text-muted mx-1"></i>
                                    {{ $competition->end_date?->format('Y/m/d') ?? '-' }}
                                </td>
                                <td>
                                    <x-status-badge domain="competition" status="{{ $competition->status }}" set="bi" />
                                </td>
                                <td>
                                    <x-status-badge domain="competition" status="{{ $competition->approval_status }}" set="bi" />
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.competitions.edit', $competition) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($competition->approval_status === 'pending')
                                        <button class="btn btn-sm btn-outline-success" style="border-radius:8px;" wire:click="approve({{ $competition->id }})">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;" wire:click="reject({{ $competition->id }})">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state py-3">
                                        <i class="bi bi-trophy d-block" style="font-size:2.5rem;"></i>
                                        <h5>لا توجد بطولات</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $competitions->links() }}</div>
</div>
