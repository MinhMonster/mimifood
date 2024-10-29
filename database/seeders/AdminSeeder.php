<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin; // Thay thế 'App\Models\Admin' bằng namespace của model Admin của bạn

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // Mã hóa mật khẩu bằng bcrypt
            // Các trường khác tùy thuộc vào cấu hình model Admin của bạn
        ]);
    }
}
