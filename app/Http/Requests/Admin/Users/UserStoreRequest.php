<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
            'image_id' => 'nullable|mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP|max:5120',
            'phone_number' => 'required_if:merchant,null',
            'shops' => 'required_if:merchant,null',
        ];
    }
}
