<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-bookmark-fill text-gold"></i> إدارة التصنيفات الفرعية</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">عرض وإدارة تصنيفات البطولات</p>
        </div>
        <a href="{{ route('admin.subtypes.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> إضافة تصنيف
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">التصنيفات</li>
        </ol>
    </nav>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <form wire:submit="resetPage" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">بحث</label>
                    <input type="text" class="form-control" placeholder="بحث بالاسم..." wire:model.live.debounce.300ms="search">
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
                            <th>الاسم (عربي)</th>
                            <th>الاسم (إنجليزي)</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subtypes as $subtype)
                            <tr wire:key="{{ $subtype->id }}">
                                <td style="color:#94a3b8;">{{ $subtype->id }}</td>
                                <td class="fw-bold">{{ $subtype->name }}</td>
                                <td style="color:#94a3b8;">{{ $subtype->en_name }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.subtypes.edit', $subtype) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                            wire:click="delete({{ $subtype->id }})"
                                            wire:confirm="هل أنت متأكد من حذف هذا التصنيف؟">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state py-3"><i class="bi bi-bookmark d-block" style="font-size:2.5rem;"></i><h5>لا توجد تصنيفات</h5></div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $subtypes->links() }}</div>
</div>
