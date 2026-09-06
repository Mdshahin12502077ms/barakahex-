<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class LoginPostRequest extends FormRequest
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
        $rules = [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
        if (setting('is_recaptcha_activated') && setting('recaptcha_site_key') && setting('recaptcha_secret')) {
            $rules['g-recaptcha-response'] = ['required'];


        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => __('Please verify that you are not a robot.'),
        ];
    }


}
