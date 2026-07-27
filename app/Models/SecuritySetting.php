<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecuritySetting extends Model
{
    use HasFactory;

    protected $table = 'security_settings';

    protected $fillable = [
        'user_id',
        'twofa_email',
        'twofa_app',
        'twofa_sms',
        'notify_on_login',
        'twofa_app_secret',
    ];

    protected $hidden = [
        'twofa_app_secret',
    ];

    protected $casts = [
        'twofa_email' => 'boolean',
        'twofa_app' => 'boolean',
        'twofa_sms' => 'boolean',
        'notify_on_login' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
