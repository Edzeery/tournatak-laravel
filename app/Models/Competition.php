<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type_id',
        'subtype_id',
        'organizer_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'location',
        'approval_status',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(CompetitionType::class, 'type_id');
    }

    public function subtype(): BelongsTo
    {
        return $this->belongsTo(CompetitionSubtype::class, 'subtype_id');
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'registrations')->withPivot('status');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Match_::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
