<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasThumbnail;

class Topic extends Model
{
    use SoftDeletes;
    use HasThumbnail;

    protected $table = 'topics';

    protected $appends = ['thumbnail'];

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
            $query->orWhere('content', 'like', '%' . $request->keyword . '%');
            $query->orWhere('description', 'like', '%' . $request->keyword . '%');
        }

        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        return $query;
    }
}
