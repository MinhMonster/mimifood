<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvatarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avatars', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password')->nullable();
            $table->json('images');
            $table->boolean('is_full_image')->default(false);
            $table->decimal('selling_price', 10, 0)->nullable();
            $table->decimal('purchase_price', 10, 0)->nullable();
            $table->decimal('discount_percent', 5, 0)->nullable();
            $table->integer('land')->nullable();
            $table->integer('pets')->nullable();
            $table->integer('fish')->nullable();
            $table->integer('sex')->default(1);
            $table->text('description');
            $table->unsignedBigInteger('author_id')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('avatars');
    }
}
