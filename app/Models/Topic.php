<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HidesTimestamps;
use App\Traits\HasThumbnail;

class Topic extends Model
{
    use SoftDeletes;
    use HidesTimestamps;
    use HasThumbnail;

    protected $appends = ['thumbnail'];

    protected $hidden = ["id", "is_active", "sort_order", "view_count", "meta_description", "meta_keywords", "meta_title", "category_id"];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'category_id',
        'user_id',
        'is_active',
        'sort_order',
        'view_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'images',
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
            $query->orWhere('content', 'like', '%' . $request->keyword . '%');
            $query->orWhere('description', 'like', '%' . $request->keyword . '%');
        }

        return $query;
    }
}
