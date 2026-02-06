<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\File;
use App\Models\Folder;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $folderId = $request->folder_id ?? null;

        $files = File::where('folder_id', $folderId);

        return formatPaginate($files, $request);
    }

    public function uploads(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif|max:51200'
        ]);

        // Chọn disk
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('main_domain');
        $diskUsed = config('filesystems.disks.main_domain.root') !== storage_path('app/public')
            ? 'main_domain'
            : 'public';

        $folder = ($request->folder_id && $request->folder_id !== 'null')
            ? Folder::find($request->folder_id)
            : null;

        if ($request->folder_id && !$folder) {
            return response()->json(['message' => 'Folder không tồn tại'], 422);
        }

        $folderPath = buildFolderPath($folder ? $folder->path : null);

        if (!$disk->exists($folderPath)) {
            $disk->makeDirectory($folderPath);
        }

        DB::beginTransaction();

        try {
            foreach ($request->file('files') as $file) {
                $fileName = $file->getClientOriginalName();
                if ($disk->exists($folderPath . $fileName)) {
                    $name = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $fileName = $name . '-' . Str::uuid() . '.' . $extension;
                }

                // Lưu file
                $disk->putFileAs($folderPath, $file, $fileName);

                File::create([
                    'name'       => $fileName,
                    'size'       => $file->getSize(),
                    'mime_type'  => $file->getClientMimeType(),
                    'folder_id' => $folder->id ?? null,
                    'disk'       => $diskUsed,
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'File uploaded successfully']);
        } catch (\Throwable $e) {

            DB::rollBack();

            // optional: log lỗi
            logger()->error('Upload failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Upload file thất bại',
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $file = File::find($request->id);

        if (!$file) {
            return response()->json(['message' => 'File không tồn tại'], 404);
        }

        // Lấy disk thực tế từ DB, fallback public nếu không có
        $diskName = $file->disk ?? 'public';
        $disk = Storage::disk($diskName);
        $filePath = $file->path;

        // Xóa file trên disk nếu tồn tại
        if ($disk->exists($filePath)) {
            $disk->delete($filePath);
        }

        // Xóa bản ghi trong DB
        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }
}
