<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'locale', 'flag', 'text_direction', 'status'];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function languageConfig(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LanguageConfig::class);
    }

    public function flag()
    {
        return $this->hasOne(FlagIcon::class, 'title', 'locale');
    }

    public function getFlagIconAttribute()
    {
        return $this->flag ? static_asset($this->flag->image) : static_asset('images/flags/ad.png');
    }


    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }

}
