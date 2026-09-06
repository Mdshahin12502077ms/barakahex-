<?php

namespace App\Repositories\Admin;

use App\Models\WebsiteService;
use App\Models\WebsiteServiceLanguage;
use App\Traits\ImageTrait;

class WebsiteServiceRepository
{
    use ImageTrait;

    public function all()
    {
        return WebsiteService::where('status', '=', '1')->with('language')->take(8)->latest()->get();
    }

    public function activeTestimonials($data = [])
    {
        return WebsiteService::where('status', 1)->when(arrayCheck('q', $data), function ($query) use ($data) {
            $query->where('name', 'like', '%'.$data['q'].'%')->orWhereHas('languages', function ($query) use ($data) {
                $query->where('name', 'like', '%'.$data['q'].'%');
            });
        })->latest()->get();
    }

    public function getByLang($id, $lang)
    {
        if (! $lang) {
            $service = WebsiteServiceLanguage::where('lang', 'en')->where('website_service_id', $id)->first();
        } else {
            $service = WebsiteServiceLanguage::where('lang', $lang)->where('website_service_id', $id)->first();
            if (! $service) {
                $service                     = WebsiteServiceLanguage::where('lang', 'en')->where('website_service_id', $id)->first();
                $service['translation_null'] = 'not-found';
            }
        }
        return $service;
    }
    public function find($id)
    {
        return WebsiteService::find($id);
    }
    public function store($request)
    {
        $service                  = new WebsiteService;

        if (isset($request['service_image'])) {
            $response                    = $this->saveImage($request['service_image'],'service_image');
            $images                      = $response['images'];
            $service->image              = $images;
        }
        $service->title               = $request['title'];
        $service->description         = $request['description'];
        $service->save();
        $this->langStore($request, $service);
        return $service;
    }

    public function update($request, $id)
    {
        $service = WebsiteService::findOrfail($id);
        if (isset($request['service_image'])) {
            $response                    = $this->saveImage($request['service_image'],'service_image');
            $images                      = $response['images'];
            $service->image              = $images;
        }
        $service->title               = $request['title'];
        $service->description         = $request['description'];
        $service->save();
        if (arrayCheck('lang', $request) && $request['lang'] != 'en') {
            $request['title ']      = $service->title;
        }if (arrayCheck('lang', $request) && $request['lang'] != 'en') {
            $request['description'] = $service->description;
        }
        if ($request['translate_id']) {
            $request['lang'] = $request['lang'] ? : 'en';
            $this->langUpdate($request);
        } else {
            $this->langStore($request, $service);
        }


        return $service;
    }

    public function destroy($id): int
    {
        return WebsiteService::destroy($id);
    }

    public function status($data)
    {
        $key         = WebsiteService::findOrfail($data['id']);
        $key->status = $data['status'];

        return $key->save();
    }

    public function langStore($request, $service)
    {

        return WebsiteServiceLanguage::create([
            'website_service_id' => $service['id'],
            'title'              => $request['title'],
            'lang'               => isset($request['lang']) ? $request['lang'] : 'en',
            'description'        => $request['description'],
        ]);
    }

    public function langUpdate($request)
    {
        return WebsiteServiceLanguage::where('id', $request['translate_id'])->update([
            'lang'           => $request['lang'],
            'title'          => $request['title'],
            'description'    => $request['description'],
        ]);
    }
}
