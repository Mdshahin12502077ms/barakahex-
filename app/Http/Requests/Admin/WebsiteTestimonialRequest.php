<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WebsiteTestimonialRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required',
            'title'       => 'required',
            'designation' => 'required',
            'description' => 'required',
        ];
    }
}
