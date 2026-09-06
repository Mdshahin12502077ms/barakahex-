<?php

namespace App\Models\Account;

use App\Models\User;
use App\Enums\StatusEnum;
use App\Models\Account\FundTransfer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class Account extends Model
{
    use HasFactory;


    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function balance(){
        return $this->hasMany(CompanyAccount::class)->sum('amount');
    }

    public function incomes(){
        return $this->hasMany(CompanyAccount::class)->where('type', 'income');
    }

    public function expenses(){
        return $this->hasMany(CompanyAccount::class)->where('type', 'expense');
    }

    public function fundReceives(){
        return $this->hasMany(FundTransfer::class, 'to_account_id', 'id');
    }

    public function fundTransfers(){
        return $this->hasMany(FundTransfer::class, 'from_account_id', 'id');
    }

    public function funds(){
        return $this->hasMany(FundTransfer::class);
    }

    public function accounts(){
        return $this->hasMany(CompanyAccount::class);
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
