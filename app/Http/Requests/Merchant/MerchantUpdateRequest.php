<?php

namespace App\Http\Requests\Merchant;

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
            'company'       => 'required',
            'trade_license' => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP,pdf|max:5120',
            'nid'           => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP,pdf|max:5120',
            'phone_number'  => 'required|between:8,30,'.\Request()->merchant,
            'website'       => 'url',
            'vat'           => 'numeric',
        ];
    }
}
