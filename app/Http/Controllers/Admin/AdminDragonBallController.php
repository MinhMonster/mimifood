<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\DragonBall;
use App\Http\Controllers\Base\AdminGameAccountController;
use Illuminate\Validation\Rule;

class AdminDragonBallController extends AdminGameAccountController
{
    protected function model(): string
    {
        return DragonBall::class;
    }

    protected function rules(?int $id = null): array
    {
        return [
            'code' => [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('dragon_balls', 'code')->ignore($id),
            ],
            'username' => [
                'required',
                'string',
                Rule::unique('dragon_balls')
                ->ignore($id)
                ->where('is_sold', false)
                ->whereNull('deleted_at'),
            ],
            'description' => 'nullable|string',
            'images' => 'required|array',
            'selling_price' => 'required|integer',
            'purchase_price' => 'required|integer',
            'discount_percent' => 'nullable|integer',
            'planet' => 'required|integer',
            'server' => 'required|integer',
            'type' => ['required', Rule::in([1, 2, 3])],
            'strength' => 'required|string',
            'disciple' => 'required|string',
        ];
    }
}
