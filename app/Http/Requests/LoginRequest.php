<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the LeftMenu is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_name' => ['required', 'string', 'min:2', 'max:255'],
            'password' => ['required', 'string', 'min:5'],
        ];
    }
    public function messages()
    {
        return [
            'user_name.required' => 'Tài khoản không được để trống',
            'user_name.min' => 'Tài khoản tối thiểu 5 ký tự',
            'user_name.max' => 'Tài khoản tối đa 255 ký tự',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' =>  'Mật khẩu tối thiểu 5 ký tự',
            'password.max' => 'Mật khẩ tối đa 128 ký tự',
        ];
    }
}
