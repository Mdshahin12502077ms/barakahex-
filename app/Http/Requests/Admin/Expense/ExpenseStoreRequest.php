<?php

namespace App\Http\Requests\Admin\Expense;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseStoreRequest extends FormRequest
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
            'date'      => 'required',
            'amount'    => 'required|max:10',
            'receipt'   => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,pdf|max:5120',
            'account'   => 'required',
            'details'   => 'required',

        ];
    }
}
