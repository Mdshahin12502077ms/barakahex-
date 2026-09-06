<?php

namespace App\Http\Requests\Admin\Bulk;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryAssignRequest extends FormRequest
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
            'delivery_man' => 'required',
            'parcel_list'  => 'required',
        ];
    }
}
