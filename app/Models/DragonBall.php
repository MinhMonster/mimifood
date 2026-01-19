<?php

namespace App\Models;

use App\Models\Base\BaseGameAccountModel;


class DragonBall extends BaseGameAccountModel
{
    protected $hidden = [
        'id',
        'username',
        'is_sold',
        'purchase_price',
        'password'
    ];

    protected $casts = [
        'images' => 'array',
        'is_sold' => 'boolean',
        'is_family' => 'boolean',
        'is_full_image' => 'boolean',
        'selling_price' => 'decimal:0',
        'purchase_price' => 'decimal:0',
        'discount_percent' => 'decimal:0',
    ];

    protected function filterableFields(): array
    {
        return [
            'code' => ['code', 'like'],
            'planet' => ['planet'],
            'server' => ['server'],
            'username' => ['username', 'like'],
            'cash' => ['selling_price', 'range'],
        ];
    }
}
