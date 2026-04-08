<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ninja;
use App\Models\Avatar;
use App\Models\DragonBall;

class UpdatePaymentOptionsSeeder extends Seeder
{
    public function run()
    {
        $this->updateModel(Ninja::class);
        $this->updateModel(Avatar::class);
        $this->updateModel(DragonBall::class);
    }

    private function updateModel($modelClass)
    {
        $modelClass::chunkById(500, function ($items) {
            foreach ($items as $item) {
                $price = $item->price; // 👉 dùng accessor

                if ($price >= 300000) {
                    $item->update([
                        'is_installments' => true,
                        'is_deposit' => true,
                        'installments_price' => round($price * 0.7),
                        'deposit_price' => round($price * 0.3),
                    ]);
                }
            }
        });
    }
}
