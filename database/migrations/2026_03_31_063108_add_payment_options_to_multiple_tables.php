<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentOptionsToMultipleTables extends Migration
{
    public function up()
    {
        $tables = ['ninjas', 'avatars', 'dragon_balls'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->boolean('is_installments')
                    ->default(false)
                    ->after('selling_price');

                $table->boolean('is_deposit')
                    ->default(false)
                    ->after('is_installments');

                $table->decimal('installments_price', 15, 0)
                    ->nullable()
                    ->after('is_deposit');

                $table->decimal('deposit_price', 15, 0)
                    ->nullable()
                    ->after('installments_price');
            });
        }
    }

    public function down()
    {
        $tables = ['ninjas', 'avatars', 'dragon_balls'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn([
                    'is_installments',
                    'is_deposit',
                    'installments_price',
                    'deposit_price',
                ]);
            });
        }
    }
}
