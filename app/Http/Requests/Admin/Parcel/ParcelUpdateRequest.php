<?php

namespace App\Http\Requests\Admin\Parcel;

use Illuminate\Foundation\Http\FormRequest;

class ParcelUpdateRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        if ($this->has('customer_phone_number')) {
            $phone = trim((string)$this->customer_phone_number);
            $phone = preg_replace('/[\s\-]/', '', $phone);
            $this->merge([
                'customer_phone_number' => $phone,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'merchant' => 'required',
            'customer_name' => 'required',
            // 'customer_invoice_no'   => 'required',
            'customer_phone_number' => [
                'required',
                'regex:/^(?:\+?88|88)?01[3-9]\d{8}$/'
            ],
            'customer_address' => 'required',
            'parcel_type' => 'required',
            'weight' => 'required',
            'price' => 'required|numeric',
            'selling_price' => 'nullable|numeric',
            'pickup_branch_id' => 'required',
            'city_id' => 'required|exists:districts,id',
            'thana_id' => 'required|exists:thanas,id',
            'district_id' => 'nullable|exists:districts,id',
            'total_quantity' => 'nullable|integer|min:1',
            'transfer_to_branch' => 'nullable',
            'destination_branch_id' => 'nullable|required_if:transfer_to_branch,1|exists:branches,id',
            'transfer_branch_select_id' => 'nullable|exists:branches,id',
        ];
    }

    public function messages()
    {
        return [
            'customer_phone_number.required' => __('The customer phone number is required.'),
            'customer_phone_number.regex' => __('Please enter a valid 11-digit Bangladeshi mobile number (e.g., 017XXXXXXXX).'),
            'pickup_branch_id.required' => 'Please update your shop pickup branch.',
            'pickup_branch_id.exists' => 'The selected pickup branch is invalid.',
            'customer_address.required' => __('Please enter a valid address.'),
            'city_id.required' => __('Please enter a valid address with a valid district.'),
            'city_id.exists' => __('Please enter a valid address with a valid district.'),
            'thana_id.required' => __('Please enter a valid address with a valid thana.'),
            'thana_id.exists' => __('Please enter a valid address with a valid thana.'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $cityId = $this->district_id ?? $this->city_id;
            if ($cityId && $this->thana_id) {
                $thanaValid = \App\Models\Thana::where('id', $this->thana_id)
                    ->where('district_id', $cityId)
                    ->where('status', 'active')
                    ->exists();
                if (!$thanaValid) {
                    $validator->errors()->add('thana_id', __('The selected thana does not belong to the selected district.'));
                    $validator->errors()->add('customer_address', __('Please enter a valid address.'));
                }
            }
        });
    }
}
