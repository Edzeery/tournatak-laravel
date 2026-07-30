<?php

namespace App\Enums;

enum ParticipantType: string
{
    case Team = 'team';
    case Individual = 'individual';
    case Both = 'both';
}
