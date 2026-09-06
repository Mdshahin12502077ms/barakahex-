<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'iso3', 'iso2', 'phonecode', 'currency', 'currency_symbol', 'latitude', 'longitude', 'status'];
    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function flag()
    {
        return $this->hasOne(FlagIcon::class, 'title', 'iso2');
    }

    public function getFlagIconAttribute()
    {
        return $this->flag ? static_asset($this->flag->image) : static_asset('images/default/default-image-40x40.png');
    }

    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }
}
