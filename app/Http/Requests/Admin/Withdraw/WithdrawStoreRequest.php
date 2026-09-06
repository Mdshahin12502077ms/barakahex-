<?php

namespace App\Http\Requests\Admin\Withdraw;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawStoreRequest extends FormRequest
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
            'amount'        => 'required|numeric|gt:0',
            'merchant'      => 'required',
            'withdraw_to'   => 'required',
        ];
    }
}
