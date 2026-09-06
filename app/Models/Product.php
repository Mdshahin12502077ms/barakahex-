<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'merchant_id',
        'name',
        'description',
    ];
    protected $casts = [
        'status' => StatusEnum::class,
    ];
}
