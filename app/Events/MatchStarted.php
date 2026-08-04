<?php

namespace App\Events;

use App\Models\Match_;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Match_ $match
    ) {}
}
