<?php
namespace App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'email'    => 'email',
            'password' => 'password',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }

    /**
     *
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * return errors
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
