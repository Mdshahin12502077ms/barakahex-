<?php

namespace App\Repositories\Admin;

use App\Models\WebsiteAbout;
use App\Models\WebsiteAboutLanguage;
use App\Traits\ImageTrait;

class WebsiteAboutRepository
{
    use ImageTrait;

    public function all()
    {
        return WebsiteAbout::where('status', '=', '1')->with('language')->take(3)->latest()->get();
    }

    public function activeTestimonials($data = [])
    {
        return WebsiteAbout::where('status', 1)->when(arrayCheck('q', $data), function ($query) use ($data) {
            $query->where('name', 'like', '%'.$data['q'].'%')->orWhereHas('languages', function ($query) use ($data) {
                $query->where('name', 'like', '%'.$data['q'].'%');
            });
        })->latest()->get();
    }

    public function getByLang($id, $lang)
    {
        if (! $lang) {
            $about = WebsiteAboutLanguage::where('lang', 'en')->where('website_about_id', $id)->first();
        } else {
            $about = WebsiteAboutLanguage::where('lang', $lang)->where('website_about_id', $id)->first();
            if (! $about) {
                $about                     = WebsiteAboutLanguage::where('lang', 'en')->where('website_about_id', $id)->first();
                $about['translation_null'] = 'not-found';
            }
        }
        return $about;
    }
    public function find($id)
    {
        return WebsiteAbout::find($id);
    }
    public function store($request)
    {
        $about                  = new WebsiteAbout;
        if (isset($request['about_icon'])) {
            $response                    = $this->saveImage($request['about_icon'],'about_icon');
            $images                      = $response['images'];
            $about->icon                 = $images;
        }
        $about->title                    = $request['title'];
        $about->description              = $request['description'];
        $about->save();
        $this->langStore($request, $about);
        return $about;
    }

    public function update($request, $id)
    {
        $about = WebsiteAbout::findOrfail($id);
        if (isset($request['about_icon'])) {
            $response                    = $this->saveImage($request['about_icon'],'about_icon');
            $images                      = $response['images'];
            $about->icon                 = $images;
        }
        $about->title                    = $request['title'];
        $about->description              = $request['description'];
        $about->save();
        if (arrayCheck('lang', $request) && $request['lang'] != 'en') {
            $request['name']    = $about['name'];
        }if (arrayCheck('lang', $request) && $request['lang']!= 'en') {
            $request['name']    = $about['name'];
        }
        if ($request['translate_id']) {
            $request['lang']    = $request['lang']? : 'en';
            $this->langUpdate($request);
        } else {
            $this->langStore($request, $about);
        }


        return $about;
    }

    public function destroy($id): int
    {
        return WebsiteAbout::destroy($id);
    }

    public function status($data)
    {
        $key         = WebsiteAbout::findOrfail($data['id']);
        $key->status = $data['status'];

        return $key->save();
    }

    public function langStore($request, $about)
    {

        return WebsiteAboutLanguage::create([
            'website_about_id' => $about['id'],
            'title'          => $request['title'],
            'lang'           => isset($request['lang'],) ? $request['lang'] : 'en',
            'description'    => $request['description'],
        ]);
    }

    public function langUpdate($request)
    {
        return WebsiteAboutLanguage::where('id', $request['translate_id'])->update([
            'lang'           => $request['lang'],
            'title'          => $request['title'],
            'description'    => $request['description'],
        ]);
    }
}
