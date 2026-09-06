<?php

namespace App\Repositories\Admin;

use App\Models\WebsiteStatistic;
use App\Models\WebsiteStatisticLanguage;
use App\Traits\ImageTrait;

class WebsiteStatisticRepository
{
    use ImageTrait;

    public function all()
    {
        return WebsiteStatistic::where('status', '=', '1')->with('language')->take(8)->latest()->get();
    }

    public function activeTestimonials($data = [])
    {
        return WebsiteStatistic::where('status', 1)->when(arrayCheck('q', $data), function ($query) use ($data) {
            $query->where('name', 'like', '%'.$data['q'].'%')->orWhereHas('languages', function ($query) use ($data) {
                $query->where('name', 'like', '%'.$data['q'].'%');
            });
        })->latest()->get();
    }

    public function getByLang($id, $lang)
    {
        if (! $lang) {
            $statistic = WebsiteStatisticLanguage::where('lang', 'en')->where('website_statistic_id', $id)->first();
        } else {
            $statistic = WebsiteStatisticLanguage::where('lang', $lang)->where('website_statistic_id', $id)->first();
            if (! $statistic) {
                $statistic                     = WebsiteStatisticLanguage::where('lang', 'en')->where('website_statistic_id', $id)->first();
                $statistic['translation_null'] = 'not-found';
            }
        }
        return $statistic;
    }
    public function find($id)
    {
        return WebsiteStatistic::find($id);
    }
    public function store($request)
    {
        $statistic                  = new WebsiteStatistic;
        if (isset($request->statistic_icon)) {
            $response                 = $this->saveImage($request->statistic_icon);
            $images                   = $response['images'];
            $statistic->icon            = $images;
        }
        $statistic->number          = $request->number;
        $statistic->title           = $request->title;
        $statistic->sub_title       = $request->sub_title;
        $statistic->save();
        $this->langStore($request, $statistic);
        return $statistic;
    }

    public function update($request, $id)
    {
        $statistic = WebsiteStatistic::findOrfail($id);
        if (isset($request->statistic_icon)) {
            $response                 = $this->saveImage($request->statistic_icon);
            $images                   = $response['images'];
            $statistic->icon          = $images;
        }
        $statistic->number           = $request->number;
        $statistic->title            = $request->title;
        $statistic->sub_title        = $request->sub_title;
        $statistic->save();
        if (arrayCheck('lang', $request) && $request->lang != 'en') {
            $request->title       = $statistic->title;
        }if (arrayCheck('lang', $request) && $request->lang != 'en') {
            $request->sub_title   = $statistic->sub_title;
        }if (arrayCheck('lang', $request) && $request->lang != 'en') {
            $request->number      = $statistic->number;
        }
        if ($request->translate_id) {
            $request->lang = $request->lang ? : 'en';
            $this->langUpdate($request);
        } else {
            $this->langStore($request, $statistic);
        }


        return $statistic;
    }

    public function destroy($id): int
    {
        return WebsiteStatistic::destroy($id);
    }

    public function status($data)
    {
        $key         = WebsiteStatistic::findOrfail($data['id']);
        $key->status = $data['status'];

        return $key->save();
    }

    public function langStore($request, $statistic)
    {

        return WebsiteStatisticLanguage::create([
            'website_statistic_id' => $statistic->id,
            'number'               => $request->number,
            'title'                => $request->title,
            'lang'                 => isset($request->lang) ? $request->lang : 'en',
            'sub_title'            => $request->sub_title,
        ]);
    }

    public function langUpdate($request)
    {
        return WebsiteStatisticLanguage::where('id', $request->translate_id)->update([
            'lang'           => $request->lang,
            'number'         => $request->number,
            'title'          => $request->title,
            'sub_title'      => $request->sub_title,
        ]);
    }
}
