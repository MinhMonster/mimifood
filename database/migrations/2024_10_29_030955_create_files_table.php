<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->bigInteger('size');
            $table->string('mime_type');
            $table->unsignedBigInteger('folder_id')->nullable();;
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            $table->unsignedBigInteger('admin_id')->nullable()->default('1');
            $table->foreign('admin_id')->references('id')->on('admins');
            // $table->foreignId('admin_id')->nullable()->default(1)->constrained('admins');
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
        Schema::dropIfExists('files');
    }
}
