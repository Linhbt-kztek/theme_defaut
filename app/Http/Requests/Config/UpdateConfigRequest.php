<?php

namespace App\Http\Requests\Config;

use App\Http\Rules\Config\CheckTimeEatConfigRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigRequest extends FormRequest
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
        return [
            // 'time_from_eat_before' => 'required',
            // 'time_eat' => [new CheckTimeEatConfigRules()]
        ];
    }

    public function messages()
    {
        return [
            'time_from_eat_before.required' => 'Trường thông tin này không được để trống!',
        ];
    }
}
