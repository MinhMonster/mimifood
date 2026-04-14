<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

abstract class BaseCrudController extends Controller
{
    /**
     * Model class (VD: User::class)
     */
    abstract protected function model(): string;

    /**
     * Rules validate
     */
    abstract protected function rules(?int $id = null): array;

    /**
     * Custom query (có thể override)
     */
    protected function query(): Builder
    {
        return $this->modelInstance()->newQuery();
    }

    /**
     * Hook trước khi save
     */
    protected function beforeSave(array &$data, ?Model $model = null): void
    {
        //
    }

    /**
     * Hook sau khi save
     */
    protected function afterSave(Model $model): void
    {
        //
    }

    protected function modelInstance(): Model
    {
        return app($this->model());
    }

    protected function findById($id, $withTrashed = true): ?Model
    {
        $query = ($this->model())::query();

        if ($withTrashed && method_exists($query->getModel(), 'bootSoftDeletes')) {
            $query->withTrashed();
        }

        return $query->find($id);
    }

    protected function notFoundResponse()
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Not found',
        ], 404);
    }

    /**
     * LIST
     */
    public function index(Request $request)
    {
        return formatPaginate($this->query(), $request);
    }

    /**
     * SHOW
     */
    public function show(Request $request)
    {
        $model = $this->findById($request->id);

        if (!$model) {
            return $this->notFoundResponse();
        }

        return fetchData($model);
    }

    /**
     * CREATE / UPDATE
     */
    public function modify(Request $request)
    {
        $id = $request->id;
        $input = $request->input('input');

        $validator = Validator::make($input, $this->rules($id));

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $model = $id
            ? $this->findById($id)
            : $this->modelInstance();

        if (!$model) {
            return $this->notFoundResponse();
        }

        $this->beforeSave($data, $model);

        $model->fill($data)->save();

        $this->afterSave($model);

        return fetchData($model);
    }

    /**
     * DELETE (soft nếu có)
     */
    public function destroy(Request $request)
    {
        $model = $this->findById($request->id);

        if (!$model) {
            return $this->notFoundResponse();
        }

        $model->delete();

        return fetchData($model);
    }

    /**
     * RESTORE (nếu có soft delete)
     */
    public function restore(Request $request)
    {
        $model = $this->findById($request->id);

        if (!$model) {
            return $this->notFoundResponse();
        }

        if (method_exists($model, 'restore')) {
            $model->restore();
        }

        return fetchData($model);
    }
}
