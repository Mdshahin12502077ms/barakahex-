<?php

namespace App\Http\Requests\Admin\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class MerchantUpdateRequest extends FormRequest
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
            'email'             => 'required|unique:users,email,'.\Request()->id,
            'phone_number'      => 'required|unique:users,phone_number,'.\Request()->id,
            'image_id'          => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP|max:5120',
            'company'           => 'required',
            'trade_license'     => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP,pdf|max:5120',
            'nid'               => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP,pdf|max:5120',
            'balance'           => 'numeric|nullable',
            'vat'               => 'numeric|nullable',
            'website'           => 'nullable|url',
        ];
    }
}
