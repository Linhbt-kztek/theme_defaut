<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:55',
            'phone' => 'digits:10',
            'email' => 'required|string|email|max:255',
            'address' => 'max:255',
            'taxCode' => 'max:55',
            'user_avatar' => 'mimes:jpeg,jpg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Họ và tên không được để trống',
            'name.string' => 'Họ và tên không hợp lệ',
            'email.string' => 'Email không được để trống',
            'email.email' => 'Địa chỉ email không hợp lệ',
            'phone' => 'Số điện thoại không hợp lệ'
        ];
    }
}
