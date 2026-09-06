<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
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
            'return_charge'                 => 'numeric|min:0',
            'fragile_charge'                => 'numeric|min:0',
            'paginate_all_list'             => 'numeric|min:1',
            'paginate_parcel_merchant_list' => 'numeric|min:1',
            'paginate_api_list'             => 'numeric|min:1',
            'pickup_accept_start'           => 'numeric|min:1',
            'pickup_accept_end'             => 'numeric|min:1',
            'outside_dhaka_days'            => 'numeric|min:0',
        ];
    }
}
