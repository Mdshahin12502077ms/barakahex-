<?php

namespace App\Models\Account;

use App\Models\Merchant;
use App\Models\Parcel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class MerchantAccount extends Model
{
    use HasFactory;

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }

    public function withdraw()
    {
        return $this->belongsTo(MerchantWithdraw::class,'merchant_withdraw_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = Sentinel::check();
            // $model->created_by = $user ? $user->id : null;
            $model->created_at = date('Y-m-d H:i:s');
        });

        static::updating(function ($model) {
            $user = Sentinel::check();
            $model->updated_by = $user ? $user->id : null;
            $model->updated_at = date('Y-m-d H:i:s');
        });
    }


}
