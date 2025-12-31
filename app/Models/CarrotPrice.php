<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarrotPrice extends Model
{
    protected $fillable = [
        'label',
        'price_label',
        'amount',
        'price',
        'normal',
        'promotion_gold',
        'promotion_x2',
        'promotion_x3',
        'promotion_diamond',
    ];
}
