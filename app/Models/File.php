<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'disk',
        'size',
        'mime_type',
        'folder_id',
        'admin_id',
        'created_at',
        'updated_at'
    ];

    protected $appends = ['byteSize', 'path', 'fileName', "url"];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'name',
        'folder',
        'admin_id',
        'mime_type',
        'path',
        'size',
        'folder_id'
    ];

    // File thuộc về một Folder
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function getFolderPathAttribute(): string
    {
        return buildFolderPath($this->folder ? $this->folder->path : null);
    }

    public function getByteSizeAttribute()
    {
        return $this->size;
    }

    public function getFileNameAttribute()
    {
        return $this->name;
    }

    // Accessor: trả về full path trên disk
    public function getPathAttribute(): string
    {
        return $this->folder_path . $this->name;
    }

    public function getUrlAttribute()
    {
        $disk = $this->disk ?? 'public';
        if ($disk === 'public') {
            $assetUrl = config('app.asset_url');
        } else {
            $assetUrl = config('app.main_domain_asset_url');
        }
        return $assetUrl . $this->path;
    }

    public function delete()
    {
        Storage::delete($this->path);
        return parent::delete();
    }
}
