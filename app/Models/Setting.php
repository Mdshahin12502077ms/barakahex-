<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'value', 'lang', 'status'];

    protected $casts = [
      'array_value' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user               = Sentinel::check();
            $model->created_at  = date('Y-m-d H:i:s');
        });

        static::updating(function ($model) {
            $user               = Sentinel::check();
            $model->updated_at  = date('Y-m-d H:i:s');
        });
    }
}
