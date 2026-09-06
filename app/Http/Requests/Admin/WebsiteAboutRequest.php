<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WebsiteAboutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|unique:website_abouts,title,'.$this->id,
            'description'  => 'required',
        ];
    }
}
