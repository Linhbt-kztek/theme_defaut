<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages() {
        return [
            'user_name.required' => 'Chưa nhập email',
            'password.required' => 'Chưa nhập password',
        ];
    }
}
