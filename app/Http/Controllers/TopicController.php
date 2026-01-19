<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index(Request $request)
    {
        $query = Topic::where('is_active', true)->search($request);

        return formatPaginate($query, $request);
    }

    public function show(string $slug)
    {
        $topic = Topic::where('slug', $slug)->where('is_active', true)->first();

        if (!$topic) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bài viết không tồn tại hoặc đã bị xoá!',
                'type' => 'topic',
            ], 404);
        }

        return fetchData($topic);
    }
}
