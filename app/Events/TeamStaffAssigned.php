<?php

namespace App\Events;

use App\Models\TeamStaff;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamStaffAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TeamStaff $teamStaff
    ) {}
}
