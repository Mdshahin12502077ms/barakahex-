<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WebsiteFeatureLanguage;

class WebsiteFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'icon',
        'title',
        'status',
    ];

    protected $casts    = [
        'icon' => 'array',
    ];


    public function languages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WebSiteFeatureLanguage::class);
    }

    public function language(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WebSiteFeatureLanguage::class, 'website_feature_id', 'id')->where('lang', app()->getLocale())->withDefault(function ($lang, $parent) {
            return $parent->hasOne(WebSiteFeatureLanguage::class, 'website_feature_id', 'id')->where('lang', 'en')->first();
        });
    }


    public function getLangTitleAttribute()
    {
        return $this->language ? $this->language->title : $this->title;
    }
}
