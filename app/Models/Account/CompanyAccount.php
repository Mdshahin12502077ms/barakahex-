<?php

namespace App\Models\Account;

use App\Models\User;
use App\Models\Parcel;
use App\Models\Merchant;
use App\Enums\StatusEnum;
use App\Models\DeliveryMan;
use App\Models\Account\Account;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account\MerchantWithdraw;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class CompanyAccount extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }


    public function withdraw()
    {
        return $this->belongsTo(MerchantWithdraw::class, 'merchant_withdraw_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function cashCollected()
    {
        return $this->belongsTo(User::class, 'cash_received_by', 'id');
    }

    public function merchantAccount()
    {
        return $this->hasOne(MerchantAccount::class, 'company_account_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
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
