<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{

     protected $guarded = ['id'];
     protected $table = "citys";
    
    public function thana()
    {
        return $this->belongsTo(\App\Model\Thana::class,'thana_id');
    }
}