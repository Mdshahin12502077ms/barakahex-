<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryTimeSet extends Model
{
    use HasFactory;
    protected $fillable = [
        'working_hours_start',
        'working_hours_end',
        'offday',
        'inside_city_days',
        'sub_city_days',
        'outside_city_days',
    ];
}
