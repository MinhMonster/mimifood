<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDragonBallsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dragon_balls', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();

            // Account info
            $table->string('username')->index();
            $table->string('password')->nullable();

            // Stats
            $table->bigInteger('strength')->nullable();
            $table->bigInteger('disciple')->nullable();
            $table->json('images')->nullable();

            // Prices
            $table->decimal('selling_price', 10, 0)->nullable();
            $table->decimal('purchase_price', 10, 0)->nullable();
            $table->decimal('discount_percent', 5, 0)->nullable();

            $table->integer('planet')->nullable();
            $table->integer('server')->nullable();
            $table->integer('type')->nullable();

            $table->text('description')->nullable();
            $table->boolean('is_sold')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('dragon_balls');
    }
}
