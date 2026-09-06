<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'thana_id',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id');
    }

    public function areas()
    {
        return $this->hasMany(Area::class, 'delivery_zone_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }
}
