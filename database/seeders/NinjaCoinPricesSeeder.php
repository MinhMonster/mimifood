<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NinjaCoinPricesSeeder extends Seeder
{
    public function run()
    {
        DB::table('ninja_coin_prices')->insert([
            [
                'id' => 1,
                'name' => 'Sv1',
                'server' => 1,
                'amount_10000' => 85,
                'amount_50000' => 90,
                'amount_200000' => 90,
                'amount_500000' => 90,
                'amount_1000000' => 90,
            ],
            [
                'id' => 2,
                'name' => 'Sv23',
                'server' => 2,
                'amount_10000' => 85,
                'amount_50000' => 91,
                'amount_200000' => 91,
                'amount_500000' => 92,
                'amount_1000000' => 92,
            ],
            [
                'id' => 3,
                'name' => 'Sv4',
                'server' => 4,
                'amount_10000' => 85,
                'amount_50000' => 91,
                'amount_200000' => 91,
                'amount_500000' => 92,
                'amount_1000000' => 92,
            ],
            [
                'id' => 4,
                'name' => 'Sv5',
                'server' => 5,
                'amount_10000' => 90,
                'amount_50000' => 95,
                'amount_200000' => 95,
                'amount_500000' => 95,
                'amount_1000000' => 97,
            ],
            [
                'id' => 5,
                'name' => 'Sv679',
                'server' => 6,
                'amount_10000' => 90,
                'amount_50000' => 95,
                'amount_200000' => 95,
                'amount_500000' => 95,
                'amount_1000000' => 97,
            ],
            [
                'id' => 6,
                'name' => 'Sv8',
                'server' => 8,
                'amount_10000' => 15,
                'amount_50000' => 15,
                'amount_200000' => 15,
                'amount_500000' => 15,
                'amount_1000000' => 15,
            ],
            [
                'id' => 7,
                'name' => 'Sv10',
                'server' => 10,
                'amount_10000' => 0,
                'amount_50000' => 0,
                'amount_200000' => 0,
                'amount_500000' => 0,
                'amount_1000000' => 0,
            ],
            [
                'id' => 8,
                'name' => 'Sv11',
                'server' => 11,
                'amount_10000' => 0,
                'amount_50000' => 0,
                'amount_200000' => 0,
                'amount_500000' => 0,
                'amount_1000000' => 0,
            ],
            [
                'id' => 9,
                'name' => 'Sv12',
                'server' => 12,
                'amount_10000' => 0,
                'amount_50000' => 0,
                'amount_200000' => 0,
                'amount_500000' => 0,
                'amount_1000000' => 0,
            ],
        ]);
    }
}
