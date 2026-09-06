<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Models\Account\Account;
use App\Models\Account\FundTransfer;
use App\Models\Account\CompanyAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class StaffAccount extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function fromAccount(){
        return $this->belongsTo(Account::class, 'from_account_id', 'id');
    }

    public function toAccount(){
        return $this->belongsTo(Account::class, 'to_account_id', 'id');
    }


    public function fundTransfer(){
        return $this->belongsTo(FundTransfer::class);
    }

    public function companyAccount()
    {
        return $this->belongsTo(CompanyAccount::class);
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
