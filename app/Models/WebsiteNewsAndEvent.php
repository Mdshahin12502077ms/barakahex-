<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteNewsAndEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'title',
        'description',
        'status',
    ];

    protected $casts    = [
        'image' => 'array',
    ];


    public function languages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WebsiteNewsAndEventLanguage::class);
    }

    public function language(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WebsiteNewsAndEventLanguage::class, 'website_news_and_event_id', 'id')->where('lang', app()->getLocale())->withDefault(function ($lang, $parent) {
            return $parent->hasOne(WebsiteNewsAndEventLanguage::class, 'website_news_and_event_id', 'id')->where('lang', 'en')->first();
        });
    }


    public function getLangDescriptionAttribute()
    {
        return $this->language ? $this->language->description : $this->description;
    }

    public function getLangTitleAttribute()
    {
        return $this->language ? $this->language->title : $this->title;
    }
}
