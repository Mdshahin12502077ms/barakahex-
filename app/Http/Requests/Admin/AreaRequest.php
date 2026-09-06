<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AreaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:191',
            'delivery_zone_id' => 'required|exists:delivery_zones,id',
            'thana_id'         => 'nullable|exists:thanas,id',
            'district_id'      => 'nullable|exists:districts,id',
            'division_id'      => 'nullable|exists:divisions,id',
            'country_id'       => 'nullable|exists:countries,id',
            'status'           => 'nullable|in:active,inactive',
        ];
    }
}
