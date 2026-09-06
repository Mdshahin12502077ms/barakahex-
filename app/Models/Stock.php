<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'type',
        'quantity',
        'status',
    ];
    protected $casts = [
        'images'          => 'array',
    ];

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
