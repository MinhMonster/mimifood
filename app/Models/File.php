<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'path',
        'size',
        'mime_type',
        'folder_id',
        'admin_id',
        'created_at',
        'updated_at'
    ];

    protected $appends = ['byteSize', 'fileName', "url"];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'name',
        'admin_id',
        'mime_type',
        'path',
        'size',
        'folder_id'
    ];

    public function getFolderPathAttribute()
    {
        if ($this->folder_id) {
            return Folder::find($this->folder_id)->path;
            // return $folder->path;
            // return $this->belongsTo(Folder::class, 'folder_id')->firts()->path;
        }

        return "/images/";
    }

    public function getByteSizeAttribute()
    {
        return $this->size;
    }

    public function getFileNameAttribute()
    {
        return $this->name;
    }

    public function getUrlAttribute()
    {
        return config('app.base_url') . "/storage" . $this->folder_path . $this->name;
    }

    public function delete()
    {
        // Xóa file thực tế trên server (nếu cần)
        Storage::delete($this->path);

        // Gọi phương thức delete mặc định để đánh dấu bản ghi là đã xóa
        return parent::delete();
    }
}
