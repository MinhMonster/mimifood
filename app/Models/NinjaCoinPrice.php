<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NinjaCoinPrice extends Model
{
    use SoftDeletes;

    protected $table = 'ninja_coin_prices';

    protected $fillable = [
        'name',
        'server',
        'amount_10000',
        'amount_50000',
        'amount_200000',
        'amount_500000',
        'amount_1000000',
    ];
}
