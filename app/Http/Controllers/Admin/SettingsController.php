<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;

class SettingsController extends Controller
{

    public function show()
    {
        $setting = Setting::find(1);

        if (!$setting) {
            return response()->json(['message' => 'Setting not found.'], 404);
        }

        return fetchData($setting);
    }
    public function modify(Request $request)
    {
        $input = $request->input('input');
        $validator = Validator::make($input, [
            'notification' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $setting = Setting::find($request->id);
        if (!$setting) {
            return response()->json(['message' => 'Setting not found.'], 404);
        }

        $setting->fill($validated);
        $setting->save();

        return fetchData($setting);

    }
}
