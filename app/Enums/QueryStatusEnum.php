<?php

namespace App\Enums;

enum QueryStatusEnum: string
{
    case ACCEPTED = 'accepted';
    case WAITING = 'waiting';
    case REJECTED = 'rejected';
    case FINISHED = 'finished';
}