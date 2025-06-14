<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('type');
        $table->datetime('start_date');
        $table->datetime('end_date');
        $table->boolean('is_active')->default(true);
        // JSON columns:
        $table->json('price_tiers')->nullable();   // Ví dụ: [{"min": 100000, "max": 200000}, ...]
        $table->softDeletes();
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
        Schema::dropIfExists('discounts');
    }
}
