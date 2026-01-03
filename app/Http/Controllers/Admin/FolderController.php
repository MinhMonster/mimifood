<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Folder;

class FolderController extends Controller
{
    const MAX_FOLDER_LEVEL = 4;

    /**
     * Hiển thị danh sách các sản phẩm.
     */
    public function index(Request $request)
    {
        $folders = Folder::whereNull('parent_id');

        return formatPaginate($folders, $request);
    }

    private function getFolderLevel(string $path): int
    {
        return count(array_filter(explode('/', trim($path, '/'))));
    }

    public function create(Request $request)
    {
        $request->validate([
            'parent_id' => 'nullable|integer|exists:folders,id',
            'name' => 'required|string|max:20|regex:/^[a-zA-Z0-9_-]+$/',
        ]);

        $parent = Folder::find($request->parent_id);
        $path = buildFolderPath(
            $parent ? $parent->path : null,
            $request->name
        );

        if ($this->getFolderLevel($path) > self::MAX_FOLDER_LEVEL) {
            return response()->json([
                'message' => 'Đã vượt quá số cấp thư mục cho phép'
            ], 422);
        }

        $disk = Storage::disk('main_domain');

        $existsInDb = Folder::where('path', $path)->exists();
        $existsInFs = $disk->exists($path);

        // DB có nhưng FS thiếu → tạo lại FS
        if ($existsInDb && !$existsInFs) {
            $disk->makeDirectory($path);
            return response()->json([
                'message' => 'Folder đã tồn tại trong DB, nhưng folder bị thiếu trên hệ thống tập tin. Đã tạo lại folder trên hệ thống tập tin.'
            ], 422);
        }

        // DB có & FS có → không làm gì
        if ($existsInDb && $existsInFs) {
            return response()->json([
                'message' => 'Thư mục đã tồn tại'
            ], 422);
        }

        // DB chưa có → tạo FS nếu cần
        if (!$existsInFs) {
            $disk->makeDirectory($path);
        }

        Folder::create([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'path' => $path,
        ]);

        return response()->json([
            'message' => 'Tạo thư mục thành công'
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:folders,id',
            'name' => 'required|string|max:20|regex:/^[a-zA-Z0-9_-]+$/',
        ]);

        $folder = Folder::findOrFail($request->id);
        $parent = Folder::find($folder->parent_id);

        $oldPath = $folder->path;
        $newPath = buildFolderPath(
            $parent ? $parent->path : null,
            $request->name
        );

        if ($oldPath === $newPath) {

            return response()->json(
                [
                    'message' => 'Folder path không thay đổi'
                ],
                422
            );
        }

        // Không cho trùng DB
        if (
            Folder::where('path', $newPath)
            ->where('id', '!=', $folder->id)
            ->exists()
        ) {
            return response()->json([
                'message' => 'Folder path đã tồn tại'
            ], 422);
        }

        $disk = Storage::disk('main_domain');

        DB::beginTransaction();

        try {
            /** ======================
             * 1️⃣ FILESYSTEM
             * ====================== */
            $oldExists = $disk->exists($oldPath);
            $newExists = $disk->exists($newPath);

            if ($oldExists && !$newExists) {
                $disk->move($oldPath, $newPath);
            }

            if ($oldExists && $newExists) {
                foreach ($disk->allFiles($oldPath) as $file) {
                    $relative = str_replace($oldPath . '/', '', $file);
                    $disk->move($file, $newPath . '/' . $relative);
                }
                $disk->deleteDirectory($oldPath);
            }

            if (!$oldExists && !$newExists) {
                $disk->makeDirectory($newPath);
            }

            /** ======================
             * 2️⃣ DATABASE (RECURSIVE)
             * ====================== */

            // Update chính nó
            $folder->update([
                'name' => $request->name,
                'path' => $newPath,
            ]);

            // Update TOÀN BỘ folder con
            Folder::where('path', 'like', $oldPath . '/%')
                ->update([
                    'path' => DB::raw(
                        "REPLACE(path, '{$oldPath}/', '{$newPath}/')"
                    )
                ]);

            DB::commit();

            return response()->json([
                'message' => 'Folder updated'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Update failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
