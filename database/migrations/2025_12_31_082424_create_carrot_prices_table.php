<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrotPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrot_prices', function (Blueprint $table) {
            $table->id();

            // Hiển thị
            $table->string('label');        // 50K, 100K, 1 Triệu
            $table->string('price_label');  // 40K, 80K, 800K

            // Giá trị tiền
            $table->integer('amount'); // mệnh giá gốc: 50000, 100000...
            $table->integer('price');  // số tiền user trả: 40000, 80000...

            // Giá trị nhận được
            $table->integer('normal');              // Lương
            $table->integer('promotion_gold');      // GOLD
            $table->integer('promotion_x2');        // KMx2
            $table->integer('promotion_x3');        // KMx3
            $table->integer('promotion_diamond');   // KMKC

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrot_prices');
    }
}
