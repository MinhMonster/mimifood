<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
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
            'files.*' => 'file|mimes:jpeg,png,jpg,gif|max:10240'
            // Adjust max size as needed
        ]);

        if ($request->folder_id && $request->folder_id !== "null") {
            $folderId = $request->folder_id;
            $folder = Folder::find($request->folder_id);
            $folderPath = $folder->path ?? "/images/";
        } else {
            $folderId = null;
            $folderPath = "/images/";
        }

        foreach ($request->file('files') as $file) {

            $fileName = $file->getClientOriginalName();
            $exists = Storage::disk('public')->exists($folderPath . $fileName);
            if ($exists) {
                // get info file
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = $fileName . '-' . time() . '.' . $fileExtension;
            }

            $fileMimeType = $file->getClientMimeType();
            $fileSize = $file->getSize();
            // upload file
            $filePath = $file->storeAs('public' .  $folderPath, $fileName);
            // change url file
            // $filePath = str_replace(
            //     'public' .  $folderPath . '/' . $fileNewName,
            //     'storage' .  $folderPath .  $fileNewName,
            //     $filePath
            // );
            // Create a new file record in the database
            // return response()->json(['message' => $filename]);

            File::create([
                'name' => $fileName,
                'path' => $filePath,
                'size' => $fileSize,
                'mime_type' => $fileMimeType,
                'folder_id' => $folderId,
            ]);
        }

        return response()->json(['message' => 'File uploaded successfully']);
    }

    public function delete(Request $request)
    {
        $file = File::find($request->id);

        // Xóa bản ghi tương ứng trong database
        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }
}
