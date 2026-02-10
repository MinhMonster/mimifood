<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Ninja;
use App\Http\Controllers\Base\AdminGameAccountController;
use Illuminate\Validation\Rule;

class AdminNinjaController extends AdminGameAccountController
{
    protected function model(): string
    {
        return Ninja::class;
    }

    protected function rules(?int $id = null): array
    {
        return array_merge(
            [
                'code' => [
                    'nullable',
                    'integer',
                    'min:1',
                    Rule::unique('ninjas', 'code')->ignore($id),
                ],
                'username' => [
                    'required',
                    'string',
                    Rule::unique('ninjas')
                        ->ignore($id)
                        ->whereNull('deleted_at'),
                ],
                'character_name' => 'required|string',
                'description' => 'required|string',
                'images' => 'required|array',
                'is_full_image' => 'required|boolean',
                'selling_price' => 'required|integer',
                'purchase_price' => 'required|integer',
                'discount_percent' => 'nullable|integer|max:50',
                'class' => 'required|integer',
                'level' => 'required|integer|max:160',
                'server' => 'required|integer',
                'weapon' => 'required|integer|max:16',
                'type' => ['required', Rule::in(['1', '2', '3'])],
            ],
            collect(range(1, 12))
                ->mapWithKeys(fn($i) => ["tl_$i" => 'nullable|integer|max:9'])
                ->all(),
            collect(range(1, 13))
                ->mapWithKeys(fn($i) => ["item_$i" => 'nullable|string'])
                ->all(),
        );
    }
}
