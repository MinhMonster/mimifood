<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();  // Tạo trường id tự động tăng
            $table->string('link')->unique(); // Link sản phẩm
            $table->string('name');  // Tên sản phẩm
            $table->text('description')->nullable();  // Mô tả sản phẩm (có thể để trống)
            $table->json('images')->nullable();  // danh sách ảnh sản phẩm
            $table->decimal('price', 10, 0);  // Giá sản phẩm
            $table->integer('quantity')->default(0);  // Số lượng sản phẩm
            $table->integer('author_id')->default(1);;  // Tên tác giả
            $table->timestamps();  // Tạo trường created_at và updated_at
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
        Schema::dropIfExists('products');
    }
}
