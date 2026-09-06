<?php

namespace App\Http\Requests\Admin\FundTransfer;

use Illuminate\Foundation\Http\FormRequest;

class FundTransferStoreRequest extends FormRequest
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
            'from_account'  => 'required',
            'to_account'    => 'required|different:from_account',
            'date'          => 'required',
            'amount'        => 'required|max:10'
        ];
    }
}
