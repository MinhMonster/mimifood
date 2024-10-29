<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folder extends Model
{
    use HasFactory;

    /**
     * Các trường có thể được gán giá trị hàng loạt (Mass Assignment).
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'parent_id', 'admin_id','created_at', 'updated_at'
    ];

    // protected $hidden = ['created_at', 'updated_at'];
}
