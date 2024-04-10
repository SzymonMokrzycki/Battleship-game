<?php

namespace App\Enums;

enum GameStatusEnum: string
{
    case WAITING = 'waiting';
    case DURINGTHEGAME = 'during the game';
    case GAMEOVER = 'game over';
}