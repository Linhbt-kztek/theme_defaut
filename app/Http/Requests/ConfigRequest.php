<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class ConfigRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(Request $request)
    {
        $required =  [
            'hotline'=>'required|string|min:1|max:20',
            'companyName' => 'required|string|max:500',
            'taxCode' => 'required|string|max:20',
            'email'=>'required|email|min:1|max:50',
            'address'=>'required|string|min:1|max:500',
            'payment_policy'=>'required|string|min:1',
            'expiry_date'=>'required|numeric|min:0|max:100',
        ];
        if($request->logo_old =="") $required["logo"] = 'required';

        return $required;
    }

    public function messages()
    {
        return [
            'hotline.required' => 'hotline không được để trống',
            'email.required' => 'email không được để trống',
            'email.email' => 'email chưa đúng format',
            'address.required' => 'address không được để trống',
            'payment_policy.required' => 'payment_policy không được để trống',
            'expiry_date.required' => 'expiry_date không được để trống',
            'expiry_date.numeric' => 'expiry_date phải là số',
            'expiry_date.min' => 'expiry_date bắt đầu từ 0',
            'logo.required' => 'logo không được để trống',
            'companyName.required' => 'Tên công ty là bắt buộc.',
            'companyName.max' => 'Tên công ty không được vượt quá 500 ký tự.',
            'taxCode.required' => 'Mã số thuế là bắt buộc.',
            'taxCode.string' => 'Mã số thuế phải là một chuỗi.',
            'taxCode.max' => 'Mã số thuế không được dài quá 20 ký tự.',

        ];
    }
}
