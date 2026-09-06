<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountUpdateRequest extends FormRequest
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
            'payment_method_id'         => 'required',
            'bank_branch'               => 'required',
            'bank_ac_name'              => 'required',
            'bank_ac_number'            => 'required',
            'routing_no'                => 'required',
        ];
    }
}
