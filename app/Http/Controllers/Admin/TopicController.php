<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Topic;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * List topics
     */
    public function index(Request $request)
    {
        $query = Topic::withTrashed()
            ->search($request)
            ->orderByDesc('id');

        return formatPaginate(
            $query,
            $request,
            [],
        );
    }

    /**
     * Create / Update topic
     */
    public function modify(Request $request)
    {
        $oldId = $request->id;
        $input = $request->input('input');

        $validator = Validator::make($input, [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'slug' => [
                'nullable',
                'string',
                Rule::unique('topics', 'slug')
                    ->ignore($oldId)
                    ->whereNull('deleted_at'),
            ],
            'description'      => 'nullable|string',
            'content'          => 'nullable|string',
            'category_id'      => 'nullable|integer',
            'is_active'        => 'required|boolean',
            'sort_order'       => 'nullable|integer',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords'    => 'nullable|string|max:255',
            'images'           => 'required|array',
            'images.*'         => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Auto slug
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        /** ========= CREATE ========= */
        if (!$oldId) {
            $topic = new Topic();
            $topic->fill($validated)->save();

            return fetchData($topic);
        }

        /** ========= UPDATE ========= */
        $oldTopic = Topic::withTrashed()->find($oldId);
        if (!$oldTopic) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Topic not found',
            ], 404);
        }

        $oldTopic->fill($validated)->save();

        return fetchData($oldTopic);
    }

    /**
     * Show topic detail
     */
    public function show(Request $request)
    {
        $topic = Topic::withTrashed()->find($request->id);

        if (!$topic) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Topic not found',
            ], 404);
        }

        return fetchData($topic);
    }

    /**
     * Toggle active / inactive topic
     */
    public function destroy(Request $request)
    {
        $topic = Topic::withTrashed()->find($request->id);

        if (! $topic) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Topic not found',
            ], 404);
        }

        $topic->update([
            'is_active' => ! $topic->is_active,
        ]);

        return fetchData($topic);
    }

    /**
     * Restore topic
     */
    public function restore(Request $request)
    {
        $topic = Topic::withTrashed()->find($request->id);

        if (!$topic) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Topic not found',
            ], 404);
        }

        if ($topic->trashed()) {
            $topic->restore();
        }

        return fetchData($topic);
    }
}
