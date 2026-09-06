<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistrictZila extends Model
{
    use HasFactory;

    protected $fillable = [
        'point_code',
        'point_name',
        'union_para_name',
        'thana_name',
        'district_name',
    ];
}
