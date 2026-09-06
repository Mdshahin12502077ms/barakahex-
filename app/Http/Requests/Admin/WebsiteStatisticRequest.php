<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WebsiteStatisticRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'         => 'required|unique:website_news_and_events,title,' . $this->id,
            'sub_title'     => 'required',
            'number'        => 'required|numeric',

        ];
    }
}
