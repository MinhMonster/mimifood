<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Folder;

class FolderController extends Controller
{
    /**
     * Hiển thị danh sách các sản phẩm.
     */
    public function index(Request $request)
    {
        $folders = Folder::whereNull('parent_id');

        return formatPaginate($folders, $request);
    }

    public function create(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'parent_id' => "nullable|integer",
            'name' => 'required|string',
        ]);

        // Tạo đường dẫn thư mục
        $folderParent = Folder::find($request->parent_id);
        // dd($request->parent_id, $folderParent->path);
        $folderPath = 'public/' . ($folderParent->path ?? "images") . "/" . $request->name;
        if (!Storage::exists($folderPath)) {
            // Tạo thư mục trên hệ thống file
            Storage::makeDirectory($folderPath);
        }

        // Kiểm tra xem bản ghi đã tồn tại trong database chưa
        $existingFolder = Folder::where('name', $request->name)->first();
        if ($existingFolder) {
            return response()->json(['message' => 'Folder already exists in database'], 422);
        } else {
            // Lưu thông tin thư mục vào database
            Folder::create([
                'parent_id' => $request->parent_id,
                'name' => $request->name,
            ]);
        }

        return response()->json(['message' => 'Folder created successfully']);
    }

    public function update(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'id' => "required|integer|exists:folders,id",
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $folder = Folder::find(request()->id);
                    if ($folder) {
                        $existingFolder = Folder::where('id', '!=', request()->id)
                            ->where('name', $value)
                            ->where('parent_id', $folder->parent_id)->get();
                        if ($folder && count($existingFolder)) {
                            return $fail('Folder already exists');
                        }
                    }
                },
            ]

        ]);

        // Tạo đường dẫn thư mục
        $folder = Folder::find($request->id);
        $folderParent = Folder::find($folder->parent_id);
        // dd($request->parent_id, $folderParent->path);
        $oldFolderPath = 'public/' . $folder->path;
        $newFolderPath = 'public/' . ($folderParent ?  $folderParent->path : "/images") . "/" . $request->name;
        if (!Storage::exists($newFolderPath)) {
            // Tạo thư mục trên hệ thống file
            Storage::move($oldFolderPath, $newFolderPath);
        }
        $folder->update([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Folder updated successfully']);
    }
}
