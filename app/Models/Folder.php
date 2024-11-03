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

    protected $appends = ['sub_folders', 'path', "parent_path"];

    /**
     *
     * @var array
     */
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

    // public function getParentAttribute()
    // {
    //     if ($this->parent_id) {
    //         return $this->belongsTo(Folder::class, 'parent_id');
    //     }

    //     return null; // Or return a default value, e.g., a new Folder instance
    // }

    // public function getParentNameAttribute()
    // {
    //     $perent = Folder::find($this->parent_id);

    //     return $perent ? $perent->name : "/images";
    // }

    public function getParentPathAttribute()
    {
        $perent = Folder::find($this->parent_id);
        $parentName = $perent ? $perent->name : "/images/";
        $parentPath = $parentName === "/images/" ? "/images/" :  $perent->path;

        return $parentPath;
    }

    public function getPathAttribute()
    {

        $full_path = $this->parent_path . $this->name . "/";
        return $full_path;
    }


}
