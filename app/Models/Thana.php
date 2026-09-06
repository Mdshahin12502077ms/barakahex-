<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thana extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'district_id',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function deliveryZones()
    {
        return $this->hasMany(DeliveryZone::class, 'thana_id');
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