<?php

namespace App\Models;

use App\Models\Base\BaseGameAccountModel;

class Ninja extends BaseGameAccountModel
{
    protected $hidden = [
        'id',
        'username',
        'password',
        'is_sold',
        'transfer_pin',
        'purchase_price'
    ];

    protected $casts = [
        'images' => 'array',
        'is_family' => 'boolean',
        'is_full_image' => 'boolean',
    ];

    protected function filterableFields(): array
    {
        return [
            'code' => ['code', 'like'],
            'character_name' => ['character_name', 'like'],
            'type' => ['type'],
            'class' => ['class'],
            'server' => ['server'],
            'level' => ['level', 'range'],
            'cash' => ['selling_price', 'range'],
            'is_family' => function ($query, $value) {
                $query->where('is_family', $value);
            },
        ];
    }
}
