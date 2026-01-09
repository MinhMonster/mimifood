<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPurchaseHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('account_purchase_histories', function (Blueprint $table) {
            $table->id();
            $table->string('account_type');
            $table->unsignedBigInteger('account_code');
            $table->json('images');
            $table->unsignedBigInteger('user_id');
            $table->decimal('selling_price', 10, 0);
            $table->decimal('purchase_price', 10, 0);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_purchase_histories');
    }
}
