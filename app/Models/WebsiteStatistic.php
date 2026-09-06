<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'icon',
        'number',
        'title',
        'subtitle',
        'status',
    ];

    protected $casts    = [
        'icon'          => 'array',
    ];


    public function languages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WebsiteStatisticLanguage::class);
    }

    public function language(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WebsiteStatisticLanguage::class, 'website_statistic_id', 'id')->where('lang', app()->getLocale())->withDefault(function ($lang, $parent) {
            return $parent->hasOne(WebsiteStatisticLanguage::class, 'website_statistic_id', 'id')->where('lang', 'en')->first();
        });
    }

    public function getLangNumberAttribute()
    {
        return $this->language ? $this->language->number : $this->number;
    }


    public function getLangSubTitleAttribute()
    {
        return $this->language ? $this->language->sub_title : $this->sub_title;
    }

    public function getLangTitleAttribute()
    {
        return $this->language ? $this->language->title : $this->title;
    }
}
