<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteAboutLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_about_id',
        'title',
        'description',
        'lang',
    ];
}
