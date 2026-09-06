<?php

namespace App\Repositories\Admin;

use App\Models\WebsiteNewsAndEvent;
use App\Models\WebsiteNewsAndEventLanguage;
use App\Traits\ImageTrait;

class WebsiteNewsAndEventRepository
{
    use ImageTrait;

    public function all()
    {
        return WebsiteNewsAndEvent::where('status', '=', '1')->with('language')->take(4)->latest()->get();
    }

    public function activeTestimonials($data = [])
    {
        return WebsiteNewsAndEvent::where('status', 1)->when(arrayCheck('q', $data), function ($query) use ($data) {
            $query->where('name', 'like', '%'.$data['q'].'%')->orWhereHas('languages', function ($query) use ($data) {
                $query->where('name', 'like', '%'.$data['q'].'%');
            });
        })->latest()->get();
    }

    public function getByLang($id, $lang)
    {
        if (! $lang) {
            $news_and_event = WebsiteNewsAndEventLanguage::where('lang', 'en')->where('website_news_and_event_id', $id)->first();
        } else {
            $news_and_event = WebsiteNewsAndEventLanguage::where('lang', $lang)->where('website_news_and_event_id', $id)->first();
            if (! $news_and_event) {
                $news_and_event                     = WebsiteNewsAndEventLanguage::where('lang', 'en')->where('website_news_and_event_id', $id)->first();
                $news_and_event['translation_null'] = 'not-found';
            }
        }
        return $news_and_event;
    }
    public function find($id)
    {
        return WebsiteNewsAndEvent::find($id);
    }
    public function store($request)
    {
        $news_and_event                  = new WebsiteNewsAndEvent;

        if (isset($request['news_event_image'])) {
            $response                    = $this->saveImage($request['news_event_image'],'news_event_image');
            $images                      = $response['images'];
            $news_and_event->image       = $images;
        }
        $news_and_event->title           = $request['title'];
        $news_and_event->description     = $request['description'];
        $news_and_event->save();
        $this->langStore($request, $news_and_event);
        return $news_and_event;
    }

    public function update($request, $id)
    {
        $news_and_event = WebsiteNewsAndEvent::findOrfail($id);
        if (isset($request['news_event_image'])) {
            $response                    = $this->saveImage($request['news_event_image'],'news_event_image');
            $images                      = $response['images'];
            $news_and_event->image       = $images;
        }
        $news_and_event->title           = $request['title'];
        $news_and_event->description     = $request['description'];
        $news_and_event->save();
        if (arrayCheck('lang', $request) && $request['lang'] != 'en') {
            $request['title']       = $news_and_event->title;
        }if (arrayCheck('lang', $request) && $request['lang'] != 'en') {
            $request['description'] = $news_and_event->description;
        }

        if ($request['translate_id']) {
            $request['lang'] = $request['lang'] ? $request['lang'] : 'en';
            $this->langUpdate($request);
        } else {
            $this->langStore($request, $news_and_event);
        }


        return $news_and_event;
    }

    public function destroy($id): int
    {
        return WebsiteNewsAndEvent::destroy($id);
    }

    public function status($data)
    {
        $key         = WebsiteNewsAndEvent::findOrfail($data['id']);
        $key->status = $data['status'];

        return $key->save();
    }

    public function langStore($request, $news_and_event)
    {

        return WebsiteNewsAndEventLanguage::create([
            'website_news_and_event_id' => $news_and_event->id,
            'title'          => $request['title'],
            'lang'           => isset($request['lang']) ? $request['lang'] : 'en',
            'description'    => $request['description'],
        ]);
    }

    public function langUpdate($request)
    {
        return WebsiteNewsAndEventLanguage::where('id', $request['translate_id'])->update([
            'lang'           => $request['lang'],
            'title'          => $request['title'],
            'description'    => $request['description']
        ]);
    }
}
