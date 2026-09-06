<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteStatisticLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_statistic_id',
        'number',
        'title',
        'lang',
        'sub_title',
    ];
}
