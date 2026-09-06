<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PercelOtpLog extends Model
{
    use HasFactory;

    protected $table = 'percel_otp_logs';

    protected $fillable = [
    'parcel_id',
    'user_id',
    'delivery_man_id',
    'action',
    'otp_code',
    'submitted_otp',
    'attempt_number',
    'source',
    'status_message',
    'ip_address',
    'user_agent',
];

// Index for fast query (parcel_id + created_at)
protected $index = ['parcel_id', 'created_at'];


public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->ip_address)) {
                $model->ip_address = request()->ip() ?: request()->server('REMOTE_ADDR');
            }
            if (empty($model->user_agent)) {
                $model->user_agent = request()->userAgent() ?: request()->server('HTTP_USER_AGENT');
            }
        });
    }

// Relationship with Parcel
public function parcel()
{
    return $this->belongsTo(Parcel::class);
}

// Relationship with User (if Admin/Staff)
public function user()
{
    return $this->belongsTo(User::class);
}

// Relationship with DeliveryMan (Rider)
public function deliveryMan()
{
    return $this->belongsTo(DeliveryMan::class);
}

}
