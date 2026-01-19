<?php

namespace App\Http\Controllers;

use App\Models\Ninja;
use App\Http\Controllers\Base\GameAccountController;


class NinjaController extends GameAccountController
{
    protected function model(): string
    {
        return Ninja::class;
    }
}
