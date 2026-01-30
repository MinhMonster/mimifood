<?php

namespace App\Http\Controllers;

use App\Models\DragonBall;
use App\Http\Controllers\Base\GameAccountController;

class DragonBallController extends GameAccountController
{
    protected function model(): string
    {
        return DragonBall::class;
    }
}
