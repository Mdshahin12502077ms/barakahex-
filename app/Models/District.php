<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'division_id',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function thanas()
    {
        return $this->hasMany(Thana::class, 'district_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }
}

