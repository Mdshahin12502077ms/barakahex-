<?php

namespace App\Models;

use App\Models\Account\MerchantAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class Parcel extends Model
{
    use HasFactory;

    protected $fillable = [
        'parcel_no',
        'packaging',
        'packaging_charge',
        'fragile',
        'fragile_charge',
        'open_box',
        'home_delivery',
        'parcel_type',
        'weight',
        'charge',
        'cod_charge',
        'vat',
        'location',
        'total_delivery_charge',
        'payable',
        'return_charge',
        'price',
        'merchant_id',
        'pickup_branch_id',
        'transfer_to_branch_id',
        'destination_branch_id',
        'user_id',
        'customer_name',
        'customer_invoice_no',
        'customer_phone_number',
        'customer_address',
        'district_id',
        'thana_id',
        'pickup_date',
        'pickup_time',
        'delivery_date',
        'delivery_time',
        'date',
        'pickup_shop_phone_number',
        'pickup_address',
        'note',
        'selling_price',
        'shop_id',
        'short_url',
        'otp_expired_at',
        'otp_attempts',
        'adjustment'
    ];

    public function scopeWithPermission($query)
    {
        if (!hasPermission('read_all_parcel') && (Sentinel::getUser()->dashboard != 'finance')) {
            $query->where(function ($q) {
                $q->where('branch_id', Sentinel::getUser()->branch_id)
                    ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                    ->orWhereNull('pickup_branch_id')
                    ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
            });
        }
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function account()
    {
        return $this->hasOne(MerchantAccount::class);
    }

    public function events()
    {
        return $this->hasMany(ParcelEvent::class)->orderByDesc('id');
    }

    public function movements()
    {
        return $this->hasMany(PercelMovement::class, 'parcel_id')->orderByDesc('id');
    }

    public function event()
    {
        return $this->hasOne(ParcelEvent::class)->where('title', 'parcel_delivered_event')->latest();
    }

    public function cancelnote()
    {
        return $this->hasOne(ParcelEvent::class)->where('title', 'parcel_cancel_event')->latest();
    }

    public function pickupPerson()
    {
        return $this->hasOne(ParcelEvent::class)->where('title', ['assign_pickup_man_event', 'parcel_re_schedule_pickup_event'])->latest();
    }

    public function deliveryPerson()
    {
        return $this->hasOne(ParcelEvent::class)->where('title', ['assign_delivery_man_event', 'parcel_re_schedule_delivery_event'])->latest();
    }

    public function deliveryMan()
    {
        return $this->hasOne(DeliveryMan::class, 'id', 'delivery_man_id');
    }

    public function pickupMan()
    {
        return $this->hasOne(DeliveryMan::class, 'id', 'pickup_man_id');
    }

    public function returnDeliveryMan()
    {
        return $this->hasOne(DeliveryMan::class, 'id', 'return_delivery_man_id');
    }

    public function transferDeliveryMan()
    {
        return $this->hasOne(DeliveryMan::class, 'id', 'transfer_delivery_man_id');
    }

    public function returnEvent()
    {
        return $this->hasOne(ParcelEvent::class)->where('title', 'parcel_returned_to_merchant_event')->latest();
    }

    public function getEvent($title)
    {
        return $this->hasOne(ParcelEvent::class)->where('title', $title)->latest();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function pickupBranch()
    {
        return $this->belongsTo(Branch::class, 'pickup_branch_id', 'id');
    }

    public function transferToBranch()
    {
        return $this->belongsTo(Branch::class, 'transfer_to_branch_id', 'id');
    }

    public function destinationBranch()
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id', 'id');
    }

    public function thirdParty()
    {
        return $this->belongsTo(ThirdParty::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function otpLogs()
    {
        return $this->hasMany(PercelOtpLog::class, 'parcel_id')->latest();
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id');
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
