<?php

namespace App\Models;
use App\Models\Merchant;
use App\Enums\StatusEnum;
use App\Enums\PaymentMethodType;
use App\Models\MerchantPaymentAccount;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PaymentMethod extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'image',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
        'image'  => 'array',
    ];


    public function payment()
    {
        $merchant = null;
        $user = jwtUser() ?? Sentinel::getUser();

        if ($user) {
            if ($user->user_type == 'merchant') {
                $merchant = Merchant::where('user_id', $user->id)->first();
            } elseif ($user->user_type == 'merchant_staff') {
                $merchant = Merchant::where('id', $user->merchant_id)->first();
            }
        }

        return $merchant ? $this->hasOne(MerchantPaymentAccount::class)->where('merchant_id', $merchant->id) : null;
    }


    public function merchantPaymentAccount(){

        return  $this->hasOne(MerchantPaymentAccount::class,'payment_method_id');
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

    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }

}
