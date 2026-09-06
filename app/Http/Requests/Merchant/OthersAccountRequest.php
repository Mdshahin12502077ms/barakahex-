<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;
class OthersAccountRequest extends FormRequest
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
        $rules = [];
        foreach ($this->input('mfs_number', []) as $key => $value) {
            $rules["mfs_number.{$key}"] = 'nullable|string|max:20';
            if (!empty($value)) {
                $rules["mfs_ac_type.{$key}"] = 'required|string';
            }
        }


        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'mfs_number.*.required' => 'The :attribute field is required.',
            'mfs_ac_type.*.required' => 'The :attribute field is required when mfs_number is not empty.',
        ];
    }
}
