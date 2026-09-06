<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'first_name'     => 'required|max:50',
            'last_name'      => 'required|max:50',
            'email'          => 'required|unique:users,email,'.\Request()->id,
            'image_id'       => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP|max:5120',
            'phone_number'   => 'required_unless:merchant,null',
            'shops'          => 'nullable|array',
        ];
    }
}
