<?php

namespace App\Models;

use App\Models\Base\BaseGameAccountModel;

class Avatar extends BaseGameAccountModel
{
    protected $fillable = [
        'username',
        'description',
        'images',
        'is_full_image',
        'selling_price',
        'discount_percent',
        'land',
        'pets',
        'fish',
        'sex',
        'is_sold'
    ];

    protected $hidden = [
        'id',
        'is_sold',
        'transfer_pin',
        'purchase_price',
        'password'
    ];

    protected $casts = [
        'images' => 'array',
        'is_full_image' => 'boolean',
    ];

    protected function filterableFields(): array
    {
        return [
            'code' => ['code', 'like'],
            'username' => ['username', 'like'],
            'land' => ['land', 'range'],
            'cash' => ['selling_price', 'range'],
            'sex' => ['sex'],
        ];
    }
}
