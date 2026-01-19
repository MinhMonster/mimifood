<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('account_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('account_type');
            $table->unsignedBigInteger('account_id')->index();
            $table->string('account_code')->index();
            $table->json('images')->nullable();
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
        Schema::dropIfExists('account_purchases');
    }
}
