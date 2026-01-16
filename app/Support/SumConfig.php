<?php

namespace App\Support;

class SumConfig
{
    public static function account_game(): array
    {
        return [
            'purchase_price',
            'selling_price' => 0.85,
            'profit' => [
                'plus'  => ['selling_price' => 0.85],
                'minus' => ['purchase_price'],
            ],
        ];
    }

    public static function for(string $type): array
    {
        switch ($type) {

            case 'account_game':
                return self::account_game();
            case 'account_purchase':
                return [
                    'purchase_price',
                    'selling_price',
                    'profit' => [
                        'plus'  => ['selling_price'],
                        'minus' => ['purchase_price'],
                    ]
                ];
            default:
                return self::account_game();
        }
    }
}
