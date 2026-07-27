<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwoFactorRecoveryCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'used_at',
    ];

    protected $hidden = [
        'code',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}
