<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        //nếu là update

        $arr = [
            'user_name' => [
                'required',
                'string',
                'min:1',
                'max:255',
                Rule::unique("users", 'user_name')->ignore($id_user)->where(function ($q) {
                    $q->where('is_delete', '=', 0);
                })
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                'min:5',
            ],
//            'email' => [
//                'required',
//                'string',
//                'max:255',
//                // Rule::unique("user",'email')->ignore($id_user)->where(function ($q) {
//                //     $q->where('is_delete', '=', 0);
//                // })
//            ],
            'phone' => 'nullable|numeric|regex:/^([0-9]{10,11})$/',
//            'address' => [
//                'nullable',
//                'string',
//                'min:1' ,
//                'max:1000',
//            ],
            'user_avatar' => [
                'nullable',
                'string',
                'min:1' ,
                'max:1000',
            ],
        ];

        //nếu là update thì ko đòi hỏi p hải nhập password
        if(isset($id_user) and $id_user !=""){
            $arr['password'] = [
                'nullable',
                'string',
                'min:1',
                'max:255',
            ];
        } else {
            //là thêm mới thì cần password
            $arr['password'] = [
                'required',
                'string',
                'min:1',
                'max:255',
            ];
        }

        return $arr;

    }

    public function messages()
    {
        //$id_user = $this->request->get('id_user');

        return [
            'name.required' => 'Tên không được để trống',
            'name.max' => 'Tên tối đa 255 ký tự',
            'name.min' => 'Tên tối thiểu 5 ký tự',

            'user_name.required' => 'Username không được để trống',
            'user_name.max' => 'Username tối đa 255 ký tự',
            'user_name.min' => 'Username tối thiểu 1 ký tự',
            'user_name.unique' => 'Username đã tồn tại',

            'password.max' => 'password tối đa 255 ký tự',
            'password.min' => 'password tối thiểu 1 ký tự',
            'password.required' => 'password không được để trống',


            'image_link.max' => 'Đường dẫn lưu ảnh tối đa 1000 ký tự',

            'phone.numeric' => 'Số điện thoại phải là số',
            'phone.regex' => 'Số điện thoại không hợp lệ',

            'user_avatar.max' => 'Avatar 500 ký tự',
            'user_avatar.min' => 'Avatar tối thiểu 1 ký tự',

            'type.required' => 'Nhóm quyền không được để trống',
            'type.min' => 'Nhóm quyền phải bắt đầu từ 1',
            'type.max' => 'Nhóm quyền không lớn hơn 3',
        ];
    }
}
