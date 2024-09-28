<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Các trường có thể được gán giá trị hàng loạt (Mass Assignment).
     *
     * @var array
     */
    protected $fillable = [
        'link', 'name', 'description', 'images', 'price', 'quantity',
    ];

    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'author_id'];


    /**
     * Các trường kiểu JSON
     *
     * @var array
     */
    protected $casts = [
        'images' => 'array',  // Laravel sẽ tự động chuyển cột images thành array khi truy xuất
    ];
}
