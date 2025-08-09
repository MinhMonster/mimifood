<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TopUpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    $user = $this->route('user');
                    if ($this->type === 'decrease' && $user->cash < $value) {
                        $fail('Số dư không đủ để trừ: '. number_format($user->cash));
                    }
                },
            ],
            'type' => ['required', Rule::in(['increase', 'decrease'])],
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'Vui lòng nhập số tiền.',
            'amount.numeric'  => 'Số tiền phải là số.',
            'amount.min'      => 'Số tiền tối thiểu là 1.',
            'type.required'   => 'Vui lòng chọn loại giao dịch.',
            'type.in'         => 'Loại giao dịch không hợp lệ.',
        ];
    }
}
