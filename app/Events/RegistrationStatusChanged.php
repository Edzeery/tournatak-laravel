<?php

namespace App\Events;

use App\Models\Registration;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegistrationStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Registration $registration,
        public string $oldStatus,
        public string $newStatus
    ) {}
}
