<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamStaff extends Model
{
    use HasFactory;

    const STAFF_ROLES = [
        'head_coach' => 'المدرب الرئيسي',
        'assistant_coach' => 'مدرب مساعد',
        'goalkeeping_coach' => 'مدرب حراس',
        'fitness_coach' => 'مدرب لياقة',
        'doctor' => 'طبيب الفريق',
        'physiotherapist' => 'معالج فيزيائي',
        'admin' => 'إداري الفريق',
        'manager' => 'المدير الرياضي',
        'nutritionist' => 'أخصائي تغذية',
        'analyst' => 'محلل أداء',
    ];

    const STAFF_ICONS = [
        'head_coach' => 'bi-award',
        'assistant_coach' => 'bi-person-workspace',
        'goalkeeping_coach' => 'bi-hand-index-up',
        'fitness_coach' => 'bi-heart-pulse',
        'doctor' => 'bi-heart',
        'physiotherapist' => 'bi-hand-thumbs-up',
        'admin' => 'bi-gear',
        'manager' => 'bi-person-badge',
        'nutritionist' => 'bi-cup-straw',
        'analyst' => 'bi-bar-chart-line',
    ];

    protected $fillable = [
        'team_id',
        'user_id',
        'staff_role',
        'specialization',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStaffRoleLabelAttribute(): string
    {
        return self::STAFF_ROLES[$this->staff_role] ?? $this->staff_role;
    }

    public function getStaffRoleIconAttribute(): string
    {
        return self::STAFF_ICONS[$this->staff_role] ?? 'bi-person';
    }
}
