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
            'keyword' => 'required|string',
        ]);

        // Tạo đường dẫn thư mục
        $folderParent = Folder::find($request->parent_id);
        // dd($request->parent_id, $folderParent->path);
        $folderPath = 'public/' . ($folderParent->path ?? "images") . "/" .$request->keyword;
        if (!Storage::exists($folderPath)) {
            // Tạo thư mục trên hệ thống file
            Storage::makeDirectory($folderPath);
        }

        // Kiểm tra xem bản ghi đã tồn tại trong database chưa
        $existingFolder = Folder::where('name', $request->keyword)->first();
        if ($existingFolder) {
            return response()->json(['message' => 'Folder already exists in database'], 422);
        } else {
            // Lưu thông tin thư mục vào database
            Folder::create([
                'parent_id' =>$request->parent_id,
                'name' => $request->keyword,
            ]);
        }




        return response()->json(['message' => 'Folder created successfully']);
    }
}
