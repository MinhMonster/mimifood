<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Folder;

class FolderController extends Controller
{
    public function create(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'keyword' => 'required|string',
        ]);

        // Tạo đường dẫn thư mục
        $folderPath = storage_path('app/folders/' . $request->keyword);
        if (!file_exists($folderPath)) {
            // Tạo thư mục trên hệ thống file
            mkdir($folderPath, 0775, true);
        }

        // Kiểm tra xem bản ghi đã tồn tại trong database chưa
        $existingFolder = Folder::where('name', $request->keyword)->first();
        if ($existingFolder) {
            return response()->json(['message' => 'Folder already exists in database'], 422);
        } else {
            // Lưu thông tin thư mục vào database
            Folder::create([
                'name' => $request->keyword,
                'path' => $folderPath,
            ]);
        }




        return response()->json(['message' => 'Folder created successfully']);
    }
}
