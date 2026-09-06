<?php

namespace App\Repositories\Admin;

use App\Models\WebsiteFeature;
use App\Models\WebsiteFeatureLanguage;
use App\Traits\ImageTrait;

class WebsiteFeatureRepository
{
    use ImageTrait;

    public function all()
    {
        return WebsiteFeature::where('status', '=', '1')->with('language')->take(8)->latest()->get();
    }

    public function activeTestimonials($data = [])
    {
        return WebsiteFeature::where('status', 1)->when(arrayCheck('q', $data), function ($query) use ($data) {
            $query->where('name', 'like', '%'.$data['q'].'%')->orWhereHas('languages', function ($query) use ($data) {
                $query->where('name', 'like', '%'.$data['q'].'%');
            });
        })->latest()->get();
    }

    public function getByLang($id, $lang)
    {
        if (! $lang) {
            $feature = WebsiteFeatureLanguage::where('lang', 'en')->where('website_feature_id', $id)->first();
        } else {
            $feature = WebsiteFeatureLanguage::where('lang', $lang)->where('website_feature_id', $id)->first();
            if (! $feature) {
                $feature                     = WebsiteFeatureLanguage::where('lang', 'en')->where('website_feature_id', $id)->first();
                $feature['translation_null'] = 'not-found';
            }
        }
        return $feature;
    }
    public function find($id)
    {
        return WebsiteFeature::find($id);
    }
    public function store($request)
    {
        $feature                         = new WebsiteFeature;

        if (isset($request['feature_icon'])) {
            $response                    = $this->saveImage($request['feature_icon'],'feature_icon');
            $images                      = $response['images'];
            $feature->icon               = $images;
        }
        $feature->title                  = $request['title'];
        $feature->save();
        $this->langStore($request, $feature);
        return $feature;
    }

    public function update($request, $id)
    {
        $feature = WebsiteFeature::findOrfail($id);
        if (isset($request['feature_icon'])) {
            $response                    = $this->saveImage($request['feature_icon'],'feature_icon');
            $images                      = $response['images'];
            $feature->icon               = $images;
        }
        $feature->title                  = $request['title'];
        $feature->save();
        if (arrayCheck('lang', $request) && $request['lang'] != 'en') {
            $request['name'] = $feature->name;
        }if (arrayCheck('lang', $request) && $request['lang'] != 'en') {
            $request['name'] = $feature->name;
        }
        if ($request['translate_id']) {
            $request['lang'] = $request['lang'] ? : 'en';
            $this->langUpdate($request);
        } else {
            $this->langStore($request, $feature);
        }


        return $feature;
    }

    public function destroy($id): int
    {
        return WebsiteFeature::destroy($id);
    }

    public function status($data)
    {
        $key         = WebsiteFeature::findOrfail($data['id']);
        $key->status = $data['status'];

        return $key->save();
    }

    public function langStore($request, $feature)
    {

        return WebsiteFeatureLanguage::create([
            'website_feature_id' => $feature['id'],
            'title'              => $request['title'],
            'lang'               => isset($request['lang']) ? $request['lang'] : 'en',
        ]);
    }

    public function langUpdate($request)
    {
        return WebsiteFeatureLanguage::where('id', $request['translate_id'])->update([
            'lang'           => $request['lang'],
            'title'          => $request['title'],
        ]);
    }
}
