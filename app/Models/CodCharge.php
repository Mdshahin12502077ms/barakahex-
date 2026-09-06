<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class CodCharge extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = Sentinel::check();
            $model->created_by = $user ? $user->id : null;
            $model->created_at = date('Y-m-d H:i:s');
        });

        static::updating(function ($model) {
            $user = Sentinel::check();
            $model->updated_by = $user ? $user->id : null;
            $model->updated_at = date('Y-m-d H:i:s');
        });
    }
}
