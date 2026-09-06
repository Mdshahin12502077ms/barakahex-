<?php

namespace App\Http\Requests\Admin\DeliveryMan;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryManUpdateRequest extends FormRequest
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
            'first_name'        => 'required|max:50',
            'last_name'         => 'required|max:50',
            'email'             => 'required|unique:users,email,'.\Request()->user_id,
            'image_id'          => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP,pdf,PDF|max:5120',
            'phone_number'      => 'required|min:6',
            'driving_license'   => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP,pdf,PDF|max:5120',
            'pick_up_fee'       => 'required|numeric',
            'delivery_fee'      => 'required|numeric',
            'return_fee'        => 'required|numeric',
        ];
    }
}
