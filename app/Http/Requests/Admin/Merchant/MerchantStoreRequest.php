<?php

namespace App\Http\Requests\Admin\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class MerchantStoreRequest extends FormRequest
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
            'last_name'         => 'max:50',
            'email'             => 'required|email|unique:users,email',
            'phone_number'      => 'required|unique:users',
            'password'          => 'required|min:6',
            'image_id'          => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP|max:5120',
            'company'           => 'required',
            'balance'           => 'numeric|nullable',
            'vat'               => 'numeric|nullable',
            'trade_license'     => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP,pdf|max:5120',
            'nid'               => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP,pdf|max:5120',
            'website'           => 'nullable|url',
            // 'g-recaptcha-response' => 'sometimes|required|captcha'
        ];
    }
}
