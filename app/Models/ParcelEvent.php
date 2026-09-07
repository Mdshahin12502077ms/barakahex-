<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class ParcelEvent extends Model
{
    use HasFactory;

    public static $lastOldStatus = [];

    protected $fillable = [
        'parcel_id',
        'user_id',
        'branch_id',
        'delivery_man_id',
        'pickup_man_id',
        'return_delivery_man_id',
        'title',
        'old_status',
        'new_status',
        'ip_address',
        'action',
        'additional_info',
    ];

    public function parcel(){
        return $this->belongsTo(Parcel::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function pickupMan(){
        return $this->hasOne(DeliveryMan::class)->latest();
    }

    public function deliveryPerson(){
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id', 'id');
    }

    public function returnPerson(){
        return $this->belongsTo(DeliveryMan::class, 'return_delivery_man_id', 'id');
    }

    public function transferPerson(){
        return $this->belongsTo(DeliveryMan::class, 'transfer_delivery_man_id', 'id');
    }

    public function pickupPerson(){
        return $this->belongsTo(DeliveryMan::class, 'pickup_man_id', 'id');
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function thirdParty(){
        return $this->belongsTo(ThirdParty::class);
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
