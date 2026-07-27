<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionType extends Model
{
    use HasFactory;

    protected $table = 'competition_types';

    protected $fillable = [
        'subtype_id',
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subtype(): BelongsTo
    {
        return $this->belongsTo(CompetitionSubtype::class, 'subtype_id');
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class, 'type_id');
    }
}
