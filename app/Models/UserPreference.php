<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'locale',
        'theme',
        'timezone',
        'date_format',
        'notify_email',
        'notify_push',
        'sidebar_collapsed',
        'density',
    ];

    protected $casts = [
        'notify_email' => 'boolean',
        'notify_push' => 'boolean',
        'sidebar_collapsed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
