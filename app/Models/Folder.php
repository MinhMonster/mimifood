<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'admin_id',
        'path',
    ];

    protected $appends = [
        'sub_folders',
        'parent_path',
    ];

    protected $casts = [
        'sub_folders' => 'array',
    ];

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function getSubFoldersAttribute()
    {
        return $this->children;
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function getParentPathAttribute(): string
    {
        $parent = $this->parent_id ? self::find($this->parent_id) : null;
        return buildFolderPath($parent ? $parent->path : null);
    }
}
