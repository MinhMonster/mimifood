<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'notification' => 'Welcome to our system!',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ],
        ]);
    }
}
