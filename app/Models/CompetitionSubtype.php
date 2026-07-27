<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionSubtype extends Model
{
    use HasFactory;

    protected $table = 'competition_subtypes';

    protected $fillable = [
        'name',
        'en_name',
    ];

    public function types(): HasMany
    {
        return $this->hasMany(CompetitionType::class, 'subtype_id');
    }
}
