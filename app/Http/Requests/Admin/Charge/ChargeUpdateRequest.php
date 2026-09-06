<?php

namespace App\Http\Requests\Admin\Charge;

use Illuminate\Foundation\Http\FormRequest;

class ChargeUpdateRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'same_day' => 'required',
            // 'next_day'         => 'required',
            'weights' => 'required|unique:charges, weight',
            'sub_city' => 'required',
            'sub_urban_area' => 'required',
        ];
    }
}
