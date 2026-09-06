<?php

namespace App\Models\Account;

use App\Models\Merchant;
use App\Models\Parcel;
use App\Models\User;
use App\Models\WithdrawBatch;
use App\Models\MerchantPaymentAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class MerchantWithdraw extends Model
{
    use HasFactory;

    protected $attribute   = ['account_details' => []];

    public function merchant(){
        return $this->belongsTo(Merchant::class);
    }

    public function account(){
        return $this->hasOne(MerchantAccount::class);
    }

    public function payments(){
        return $this->belongsTo(MerchantPaymentAccount::class, 'withdraw_to', 'id');
    }

    public function merchantPaymentAccount(){
        return $this->belongsTo(MerchantPaymentAccount::class, 'withdraw_to', 'id');
    }


    public function companyAccount(){
        return $this->hasOne(CompanyAccount::class)->latest();
    }

    public function companyAccountFirst(){
        return $this->hasOne(CompanyAccount::class)->first();
    }

    public function companyAccountReason(){
        return $this->hasOne(CompanyAccount::class)->latest();
    }

    public function parcels(){
        return $this->hasMany(Parcel::class, 'withdraw_id', 'id');
    }

    public function merchantAccounts(){
        return $this->hasMany(MerchantAccount::class, 'payment_withdraw_id', 'id');
    }

    public function paidReverseParcels(){
        return $this->hasMany(MerchantAccount::class, 'parcel_withdraw_id', 'id');
    }

    public function approvedBy(){
        return $this->belongsTo(\App\Models\User::class, 'approved_by', 'id');
    }

    public function paidBy(){
        return $this->belongsTo(\App\Models\User::class, 'paid_by', 'id');
    }

    public function withdrawBatch(){
        return $this->belongsTo(WithdrawBatch::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'created_by', 'id');
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
