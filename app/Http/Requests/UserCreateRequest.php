<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserCreateRequest extends FormRequest
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
    public function rules()
    {

        $id_user = $this->request->get('id_user');

        //là edit
        if(isset($id_user) and $id_user !=""){
            return [
                'user_name' => [
                    'required',
                    'string',
                    'max:255',
                    'min:1' ,
                    Rule::unique("users",'user_name')->ignore($id_user)->where(function ($q) {
                        $q->where('is_delete', '=', 0);
                    })
                ],
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'min:1' ,
                ],
//                'email' => [
//                    'required',
//                    'string',
//                    'max:255',
//                    Rule::unique("users",'email')->ignore($id_user)->where(function ($q) {
//                        $q->where('is_delete', '=', 0);
//                    })
//                ],
                'password' => [
                    'nullable',
                    'string',
                    'min:1' ,
                    'max:255',
                ],
                'phone' => [
                    'nullable',
                    'string',
                    'min:1' ,
                    'max:255',
                ],
                'address' => [
                    'nullable',
                    'string',
                    'min:1' ,
                    'max:1000',
                ],
                'user_avatar' => [
                    'nullable',
                    'string',
                    'min:1' ,
                    'max:1000',
                ],
              'type' => ['required', 'numeric', 'max:3','min:1'],
            ];
        } else {
            //là thêm mới
            return [
                'user_name' => [
                    'required',
                    'string',
                    'max:255',
                    'min:5',
                    Rule::unique("users",'user_name')->ignore($id_user)->where(function ($q) {
                        $q->where('is_delete', '=', 0);
                    })
                ],
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'min:3' ,
                ],
//                'email' => [
//                    'required',
//                    'string',
//                    'max:255',
//                    Rule::unique("users",'email')->ignore($id_user)->where(function ($q) {
//                        $q->where('is_delete', '=', 0);
//                    })
//                ],
                'password' => [
                    'required',
                    'string',
                    'min:3' ,
                    'max:255',
                ],
                'phone' => [
                    'nullable',
                    'string',
                    'min:1' ,
                    'max:255',
                ],
                'address' => [
                    'nullable',
                    'string',
                    'min:1' ,
                    'max:1000',
                ],
                'user_avatar' => [
                    'nullable',
                    'string',
                    'min:1' ,
                    'max:1000',
                ],
               'type' => ['required', 'numeric', 'max:2','min:1'],
            ];
        }



    }

    public function messages()
    {
        //$id_user = $this->request->get('id_user');

        return [
            'user_name.required' => 'user_name không được để trống',
            'user_name.max' => 'user_name tối đa 255 ký tự',
            'user_name.min' => 'user_name tối thiểu 5 ký tự',
            'user_name.unique' => 'user_name đã tồn tại',

            'name.required' => 'Tên không được để trống',
            'name.max' => 'Tên tối đa 255 ký tự',
            'name.min' => 'Tên tối thiểu 3 ký tự',

            'email.required' => 'Email không được để trống',
            'email.max' => 'Email tối đa 255 ký tự',
            'email.min' => 'Email tối thiểu 1 ký tự',
            'email.unique' => 'Email đã tồn tại',

            'password.required' => 'password không được để trống',
            'password.max' => 'password tối đa 255 ký tự',
            'password.min' => 'password tối thiểu 3 ký tự',

          //  'phone.required' => 'phone không được để trống',
            'phone.max' => 'phone tối đa 255 ký tự',
            'phone.min' => 'phone tối thiểu 1 ký tự',

           // 'address.required' => 'Địa chỉ không được để trống',
            'address.max' => 'Địa chỉ tối đa 500 ký tự',
            'address.min' => 'Địa chỉ tối thiểu 1 ký tự',

            'user_avatar.max' => 'Avatar 500 ký tự',
            'user_avatar.min' => 'Avatar tối thiểu 1 ký tự',


            'type.required' => 'Nhóm quyền không được để trống',
            'type.min' => 'Nhóm quyền phải bắt đầu từ 1',
            'type.max' => 'Nhóm quyền không lớn hơn 3',
        ];
    }
}
