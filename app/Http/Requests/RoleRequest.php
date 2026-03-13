<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
        $id = $this->request->get('id');

        //edit
        if(isset($id) and $id !=""){

            return [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'min:3' ,
                    Rule::unique("roles",'name')->ignore($id)->where(function ($q) {
                        $q->where('is_delete', '=', 0);
                    })
                ],

                'description' => [
                    'nullable',
                    'string',
                    'min:1' ,
                    'max:255',
                ],

            ];
        } else {
            //là thêm mới
            return [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'min:3' ,
                    Rule::unique("roles",'name')->where(function ($q) {
                        $q->where('is_delete', '=', 0);
                    })
                ],

                'description' => [
                    'nullable',
                    'string',
                    'min:1' ,
                    'max:255',
                ],

            ];
        }



    }

    public function messages()
    {
        //$id_user = $this->request->get('id_user');

        return [

            'name.required' => 'Tên không được để trống',
            'name.max' => 'Tên tối đa 255 ký tự',
            'name.min' => 'Tên tối thiểu 3 ký tự',
            'name.unique' => 'Tên đã tồn tại',

            'description.required' => 'Mô tả không được để trống',
            'description.max' => 'Mô tả  tối đa 500 ký tự',
            'description.min' => 'Mô tả  tối thiểu 1 ký tự',

        ];
    }
}
