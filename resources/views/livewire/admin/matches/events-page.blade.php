<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.index') }}" class="text-decoration-none" style="color:var(--primary);">المباريات</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.edit', $match) }}" class="text-decoration-none" style="color:var(--primary);">{{ $match->team1->name ?? '?' }} vs {{ $match->team2->name ?? '?' }}</a></li>
            <li class="breadcrumb-item active">الأحداث</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-lightning-fill text-gold"></i> أحداث المباراة</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">
                {{ $match->team1->name ?? '?' }}
                <span class="text-gold fw-bold mx-1">vs</span>
                {{ $match->team2->name ?? '?' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-warning" wire:click="$set('showModal', true)">
                <i class="bi bi-plus-lg"></i> إضافة حدث
            </button>
            <a href="{{ route('admin.matches.edit', $match) }}" class="btn btn-outline-secondary" style="border-radius:8px;">
                <i class="bi bi-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="card border-0" wire:loading.opacity>
        <div class="card-body">
            @forelse($events as $event)
                @php
                    $typeColors = [
                        'goal' => ['bg' => 'success', 'icon' => 'bi-circle-fill'],
                        'own_goal' => ['bg' => 'success', 'icon' => 'bi-circle-fill'],
                        'penalty_scored' => ['bg' => 'success', 'icon' => 'bi-circle-fill'],
                        'penalty_missed' => ['bg' => 'danger', 'icon' => 'bi-circle-fill'],
                        'yellow_card' => ['bg' => 'warning', 'icon' => 'bi-square-fill'],
                        'second_yellow' => ['bg' => 'warning', 'icon' => 'bi-square-fill'],
                        'red_card' => ['bg' => 'danger', 'icon' => 'bi-square-fill'],
                        'substitution_in' => ['bg' => 'primary', 'icon' => 'bi-arrow-left-right'],
                        'substitution_out' => ['bg' => 'primary', 'icon' => 'bi-arrow-left-right'],
                        'injury' => ['bg' => 'danger', 'icon' => 'bi-heart-pulse'],
                        'save' => ['bg' => 'info', 'icon' => 'bi-handbag'],
                        'assist' => ['bg' => 'info', 'icon' => 'bi-arrow-return-left'],
                    ];
                    $color = $typeColors[$event->event_type] ?? ['bg' => 'secondary', 'icon' => 'bi-circle'];
                    $eventTeam = $event->team_id == $match->team1_id ? $match->team1 : $match->team2;
                @endphp
                <div class="d-flex align-items-start gap-3 mb-3 p-3 rounded-3" style="background:#f8f9fa;" wire:key="event-{{ $event->id }}">
                    {{-- Minute Badge --}}
                    <div class="text-center flex-shrink-0" style="min-width:60px;">
                        <span class="badge bg-dark rounded-pill" style="font-size:0.9rem;min-width:50px;">
                            {{ $event->minute }}'
                            @if($event->added_time)
                                +{{ $event->added_time }}
                            @endif
                        </span>
                    </div>

                    {{-- Event Icon --}}
                    <div class="flex-shrink-0 mt-1">
                        <span class="badge bg-{{ $color['bg'] }} rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                            <i class="bi {{ $color['icon'] }}"></i>
                        </span>
                    </div>

                    {{-- Event Details --}}
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-{{ $color['bg'] }} text-white" style="font-size:0.8rem;">
                                {{ $eventTypes[$event->event_type] ?? $event->event_type }}
                            </span>
                            <strong>{{ $event->player->name ?? '-' }}</strong>
                            <span class="text-muted">·</span>
                            <small class="text-muted">{{ $eventTeam->name ?? '-' }}</small>
                        </div>
                        @if(in_array($event->event_type, ['substitution_in', 'substitution_out']) && $event->relatedPlayer)
                            <div class="mt-1" style="font-size:0.85rem;">
                                <i class="bi bi-arrow-left-right text-primary"></i>
                                <span class="text-muted">اللاعب المتعلق:</span>
                                <strong>{{ $event->relatedPlayer->name }}</strong>
                            </div>
                        @endif
                        @if($event->description)
                            <div class="mt-1">
                                <small class="text-muted" style="font-size:0.85rem;"><i class="bi bi-chat-left-text"></i> {{ $event->description }}</small>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex gap-1 flex-shrink-0">
                        <button class="btn btn-sm btn-outline-primary" style="border-radius:8px;" wire:click="editEvent({{ $event->id }})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                wire:click="deleteEvent({{ $event->id }})"
                                wire:confirm="هل أنت متأكد من حذف هذا الحدث؟">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-state py-5 text-center">
                    <i class="bi bi-calendar-event d-block text-muted" style="font-size:3rem;"></i>
                    <h5 class="text-muted mt-2">لا توجد أحداث</h5>
                    <p class="text-muted" style="font-size:0.85rem;">لم يتم تسجيل أي أحداث لهذه المباراة بعد</p>
                    <button class="btn btn-warning mt-2" wire:click="$set('showModal', true)">
                        <i class="bi bi-plus-lg"></i> إضافة أول حدث
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    @if($showModal)
        <div class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);" wire:click.self="closeModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" @click.away="closeModal">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-lightning-fill text-gold"></i>
                            {{ $editingEventId ? 'تعديل الحدث' : 'إضافة حدث' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">الفريق</label>
                                <select class="form-select" wire:model.live="eventForm.team_id">
                                    <option value="">-- اختر الفريق --</option>
                                    <option value="{{ $match->team1_id }}">{{ $match->team1->name ?? 'الفريق الأول' }}</option>
                                    <option value="{{ $match->team2_id }}">{{ $match->team2->name ?? 'الفريق الثاني' }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">نوع الحدث</label>
                                <select class="form-select" wire:model="eventForm.event_type">
                                    <option value="">-- اختر النوع --</option>
                                    @foreach($eventTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">اللاعب</label>
                                <select class="form-select" wire:model="eventForm.player_id">
                                    <option value="">-- اختر اللاعب --</option>
                                    @foreach($activeTeamPlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }} ({{ $player->number ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">اللاعب المتعلق</label>
                                <select class="form-select" wire:model="eventForm.related_player_id" {{ in_array($eventForm['event_type'], ['substitution_in', 'substitution_out']) ? '' : 'disabled' }}>
                                    <option value="">-- لا يوجد --</option>
                                    @foreach($activeTeamPlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }} ({{ $player->number ?? '-' }})</option>
                                    @endforeach
                                </select>
                                @if(!in_array($eventForm['event_type'], ['substitution_in', 'substitution_out']))
                                    <small class="text-muted" style="font-size:0.8rem;">فقط لعمليات التبديل</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">الدقيقة</label>
                                <input type="number" class="form-control" wire:model="eventForm.minute" min="0" max="120" placeholder="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">الوقت المضاف</label>
                                <input type="number" class="form-control" wire:model="eventForm.added_time" min="0" max="15" placeholder="0">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">الوصف</label>
                                <textarea class="form-control" wire:model="eventForm.description" rows="2" placeholder="وصف الحدث..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">إلغاء</button>
                        <button type="button" class="btn btn-warning px-4" wire:click="saveEvent" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveEvent"><i class="bi bi-check-lg"></i> حفظ</span>
                            <span wire:loading wire:target="saveEvent"><span class="spinner-border spinner-border-sm"></span> جاري الحفظ...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
