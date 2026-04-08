<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFlowToAccountPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_purchases', function (Blueprint $table) {
            $table->string('type')->default('normal')->after('purchase_price');
            $table->string('status')->default('completed')->after('type');
            $table->unsignedBigInteger('first_paid_amount')->nullable()->after('status');
            $table->unsignedBigInteger('second_paid_amount')->nullable()->after('first_paid_amount');
            $table->timestamp('deadline_at')->nullable()->after('second_paid_amount');
            $table->timestamp('cancelled_at')->nullable()->after('deadline_at');
            $table->timestamp('completed_at')->nullable()->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_purchases', function (Blueprint $table) {
            //
        });
    }
}
