<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNinjasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ninjas', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();
            $table->string('username');
            $table->string('password')->nullable();
            $table->string('character_name');
            $table->json('images');
            $table->boolean('is_full_image')->default(true);;
            $table->boolean('is_family')->default(false);;
            $table->decimal('selling_price', 10, 0)->nullable();
            $table->decimal('purchase_price', 10, 0)->nullable();
            $table->decimal('discount_percent', 5, 0)->nullable();
            $table->string('transfer_pin', 255)->nullable();
            $table->integer('class');
            $table->integer('level');
            $table->integer('server');
            $table->integer('weapon');
            $table->integer('type');
            $table->text('description');
            $table->boolean('is_sold')->default(false);
            $table->unsignedBigInteger('author_id')->default(1);

            for ($i = 1; $i <= 12; $i++) {
                $table->integer("tl_{$i}")->nullable();
            }

            for ($i = 1; $i <= 13; $i++) {
                $table->string("item_{$i}")->nullable();
            }

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
        Schema::dropIfExists('ninjas');
    }
}
