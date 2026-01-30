<?php

namespace App\Http\Controllers;

use App\Models\Avatar;
use App\Http\Controllers\Base\GameAccountController;

class AvatarController extends GameAccountController
{
    protected function model(): string
    {
        return Avatar::class;
    }
}
