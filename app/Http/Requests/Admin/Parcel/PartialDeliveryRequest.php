<?php

namespace App\Http\Requests\Admin\Parcel;

use Illuminate\Foundation\Http\FormRequest;

class PartialDeliveryRequest extends FormRequest
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
        'cod' => 'required',
        'delivered_quantity' => 'required|numeric|min:1',
        'return_quantity'    => 'nullable|numeric|min:0',
        'payment_method'     => 'nullable|string',
        'note'               => 'nullable|string',
    ];
    }
}
