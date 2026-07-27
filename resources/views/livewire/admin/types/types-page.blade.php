<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-tags-fill text-gold"></i> إدارة أنواع البطولات</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">عرض وإدارة أنواع البطولات والتصنيفات</p>
        </div>
        <a href="{{ route('admin.types.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> إضافة نوع
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">الأنواع</li>
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
                            <th>الاسم</th>
                            <th>الرابط</th>
                            <th>التصنيف</th>
                            <th>الحالة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                            <tr wire:key="{{ $type->id }}">
                                <td style="color:#94a3b8;">{{ $type->id }}</td>
                                <td class="fw-bold">{{ $type->name }}</td>
                                <td><code style="background:rgba(255,193,7,0.1);color:#b8860b;padding:2px 8px;border-radius:4px;font-size:0.8rem;">{{ $type->slug }}</code></td>
                                <td>{{ $type->subtype->name ?? '-' }}</td>
                                <td>
                                    @if($type->is_active)
                                        <x-status-badge domain="competition" status="active" set="bi" />
                                    @else
                                        <x-status-badge domain="competition" status="inactive" set="bi" />
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning" style="border-radius:8px;" wire:click="toggleActive({{ $type->id }})">
                                        <i class="bi bi-toggle-{{ $type->is_active ? 'on' : 'off' }}"></i>
                                    </button>
                                    <a href="{{ route('admin.types.edit', $type) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                            wire:click="delete({{ $type->id }})"
                                            wire:confirm="هل أنت متأكد من حذف هذا النوع؟">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state py-3"><i class="bi bi-tags d-block" style="font-size:2.5rem;"></i><h5>لا توجد أنواع</h5></div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $types->links() }}</div>
</div>
