<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    use SoftDeletes;

    protected $table = 'topics';

    protected $fillable = [
        'id',
        'title',
        'slug',
        'images',
        'description',
        'content',
        'category_id',
        'is_active',
        'sort_order',
        'view_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'images' => 'array',
    ];


    /* ========= SEARCH SCOPE ========= */
    public function scopeSearch($query, $request)
    {
        if ($request->keyword) {
            $query->where('title', 'like', '%' . $request->keyword . '%');
        }

        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        return $query;
    }
}
