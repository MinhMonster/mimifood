<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class GameAccountController extends Controller
{
    /**
     * Trả về Model class (VD: DragonBall::class)
     *
     * @return class-string<Model>
     */
    abstract protected function model(): string;

    /**
     * Lấy instance của Model
     */
    protected function modelInstance(): Model
    {
        return app($this->model());
    }

    /**
     * Query chung cho index
     * Controller con có thể override nếu cần
     */
    protected function baseQuery(): Builder
    {
        return $this->modelInstance()
            ->newQuery()
            ->available()
            ->filter()
            ->orderByDesc('code');
    }

    /**
     * Danh sách public account
     */
    public function index(Request $request)
    {
        return formatPaginate(
            $this->baseQuery(),
            $request
        );
    }

    /**
     * Chi tiết account theo code
     */
    public function show(string $code)
    {
        $item = $this->modelInstance()
            ->newQuery()
            ->available()
            ->where('code', $code)
            ->first();

        if (!$item) {
            return $this->notFoundResponse();
        }

        return fetchData($item);
    }

    /**
     * Response khi không tìm thấy account
     */
    protected function notFoundResponse()
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Tài khoản không tồn tại hoặc đã bán!',
            'account_type' => $this->accountType(),
        ], 404);
    }

    /**
     * Tự suy ra account_type từ tên model
     * DragonBall => dragon_ball
     */
    protected function accountType(): string
    {
        return Str::snake(class_basename($this->model()));
    }
}
