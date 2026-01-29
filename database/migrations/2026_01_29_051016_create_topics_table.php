<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();

            // Nội dung chính
            $table->string('title');
            $table->string('slug')->unique();
            $table->json('images');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();

            // Phân loại / quan hệ
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('author_id')->default(1);

            // Trạng thái
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('view_count')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['category_id', 'is_active']);
            $table->index('author_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
