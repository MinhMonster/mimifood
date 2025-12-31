<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNinjaCoinPricesTable extends Migration
{
    public function up()
    {
        Schema::create('ninja_coin_prices', function (Blueprint $table) {
            $table->id();

            $table->string('name');          // Sv1, Sv23...
            $table->integer('server');       // server id

            $table->integer('amount_10000')->default(0);
            $table->integer('amount_50000')->default(0);
            $table->integer('amount_200000')->default(0);
            $table->integer('amount_500000')->default(0);
            $table->integer('amount_1000000')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ninja_coin_prices');
    }
}
