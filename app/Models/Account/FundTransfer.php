<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account\Account;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class FundTransfer extends Model
{
    use HasFactory;

    public function fromAccount(){
        return $this->belongsTo(Account::class, 'from_account_id', 'id');
    }

    public function toAccount(){
        return $this->belongsTo(Account::class, 'to_account_id', 'id');
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
