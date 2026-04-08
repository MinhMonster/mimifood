<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Avatar; // Import model
use App\Http\Controllers\Base\AdminGameAccountController;
use Illuminate\Validation\Rule;


class AdminAvatarController extends AdminGameAccountController
{
    protected function model(): string
    {
        return Avatar::class;
    }

    protected function rules(?int $id = null): array
    {
        return [
            'code' => [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('avatars', 'code')->ignore($id),
            ],
            'username' => [
                'required',
                'string',
                Rule::unique('avatars')
                    ->ignore($id)
                    ->where('is_sold', false)
                    ->whereNull('deleted_at'),
            ],
            'description' => 'required|string',
            'images' => 'required|array',
            'is_full_image' => 'required|boolean',
            'is_installments' => 'nullable|boolean',
            'is_deposit' => 'nullable|boolean',
            'selling_price' => 'required|integer',
            'purchase_price' => 'required|integer',
            'deposit_price' => 'nullable|integer',
            'installments_price' => 'nullable|integer',
            'discount_percent' => 'nullable|integer',
            'land' => 'required|integer',
            'pets' => 'required|integer',
            'fish' => 'required|integer',
            'sex' => 'required|integer',
        ];
    }
}
