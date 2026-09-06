<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteNewsAndEventLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_news_and_event_id',
        'title',
        'description',
        'lang',
    ];
}
