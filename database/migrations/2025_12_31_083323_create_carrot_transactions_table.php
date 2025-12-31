<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrotTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrot_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            /* ================= GAME INFO ================= */
            $table->string('game_type', 20)->default('ninja');
            $table->string('username');
            $table->integer('server');
            $table->integer('amount');
            $table->integer('price');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('admin_note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /* ================= INDEX ================= */
            $table->index(['user_id', 'status']);
            $table->index(['game_type', 'server']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrot_transactions');
    }
}
