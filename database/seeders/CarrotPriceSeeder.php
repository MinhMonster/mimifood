<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarrotPrice;

class CarrotPriceSeeder extends Seeder
{
    public function run()
    {
        $cards = [
            [
                'label' => '50K',
                'price_label' => '40K',
                'amount' => 50000,
                'price' => 40000,
                'normal' => 91,
                'promotion_gold' => 161,
                'promotion_x2' => 141,
                'promotion_x3' => 235,
                'promotion_diamond' => 273,
            ],
            [
                'label' => '100K',
                'price_label' => '80K',
                'amount' => 100000,
                'price' => 80000,
                'normal' => 195,
                'promotion_gold' => 345,
                'promotion_x2' => 295,
                'promotion_x3' => 495,
                'promotion_diamond' => 585,
            ],
            [
                'label' => '200K',
                'price_label' => '160K',
                'amount' => 200000,
                'price' => 160000,
                'normal' => 455,
                'promotion_gold' => 855,
                'promotion_x2' => 805,
                'promotion_x3' => 1155,
                'promotion_diamond' => 1275,
            ],
            [
                'label' => '500K',
                'price_label' => '400K',
                'amount' => 500000,
                'price' => 400000,
                'normal' => 1430,
                'promotion_gold' => 2680,
                'promotion_x2' => 2530,
                'promotion_x3' => 3630,
                'promotion_diamond' => 4290,
            ],
            [
                'label' => '1 Triệu',
                'price_label' => '800K',
                'amount' => 1000000,
                'price' => 800000,
                'normal' => 3250,
                'promotion_gold' => 6250,
                'promotion_x2' => 5750,
                'promotion_x3' => 8250,
                'promotion_diamond' => 9750,
            ],
        ];

        foreach ($cards as $card) {
            CarrotPrice::create($card);
        }
    }
}
