<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{

    public function notification()
    {
        $setting = Setting::find(1);

        if (!$setting) {
            return response()->json(['message' => 'Setting not found.'], 404);
        }

        return response()->json([
            'data' => $setting->notification,
        ]);
    }
}
