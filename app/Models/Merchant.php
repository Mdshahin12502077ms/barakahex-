<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Models\Account\CompanyAccount;
use App\Models\Account\MerchantAccount;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account\MerchantWithdraw;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'company',
        'phone_number',
        'handling_fee',
        'parcel_rate',
    ];

    protected $casts = [
        'charges' => 'array',
        'cod_charges' => 'array',
        'nid' => 'array',
        'trade_license' => 'array',
        'status' => StatusEnum::class,

    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parcels()
    {
        return $this->hasMany(Parcel::class)->latest();
    }

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class)->latest();
    }

    public function withdraws()
    {
        return $this->hasMany(MerchantWithdraw::class)->latest();
    }

    public function paymentAccount()
    {
        return $this->hasone(MerchantPaymentAccount::class);
    }

    public function accountStatements()
    {
        return $this->hasMany(MerchantAccount::class)->orderByDesc('id');
    }

    public function paymentLogs()
    {
        return $this->hasMany(MerchantAccount::class);
    }

    public function merchantAccount()
    {
        return $this->belongsTo(MerchantAccount::class, 'id', 'merchant_id')->where('source', 'opening_balance');
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function scopeWithPermission($query, $id)
    {
        if (hasPermission('read_all_merchant')) {
            return $query->where('id', $id);
        }
        return $query->where('id', $id)
            ->whereHas('shops', function ($query) {
                $query->where('pickup_branch_id', Sentinel::getUser()->branch_id);
            });
    }

    public function balance($id)
    {
        $parcels = Parcel::where('merchant_id', $id)
            ->where(function ($query) {
                $query->where('is_partially_delivered', '=', 1)
                    ->orWhereIn('status', ['delivered', 'delivered-and-verified']);
            })
            ->where("withdraw_id", "=", null)
            ->where('is_paid', false);

        $payable = $parcels->sum('payable');

        $accounts = MerchantAccount::selectRaw("
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
    ")
            ->where('merchant_id', $id)
            ->where(function ($query) {
                $query->whereIn('source', [
                    'previous_balance',
                    'cash_given_for_delivery_charge',
                    'parcel_return',
                    'paid_parcels_delivery_reverse',
                    'opening_balance'
                ])
                    ->orWhere(function ($query) {
                        $query->where('source', 'vat_adjustment')
                            ->whereIn('details', [
                                'govt_vat_for_parcel_return',
                                'govt_vat_for_parcel_return_reversed'
                            ]);
                    });
            })
            ->whereNull('payment_withdraw_id')
            ->where('is_paid', false)
            ->first();

        $balance = $payable + ($accounts->total_income ?? 0) - ($accounts->total_expense ?? 0);

        return $balance;
    }



    public function payableBalance($id)
    {
        $user = Sentinel::getUser();
        $userType = $user->user_type;
        $shopField = $user->shops;
        $parcels = Parcel::where('merchant_id', $id)
            ->where(function ($query) {
                $query->where('is_partially_delivered', '=', 1)
                    ->orWhereIn('status', ['delivered', 'delivered-and-verified']);
            })
            ->when($userType === 'merchant_staff' && !in_array('all_parcel_payment', $user->permissions ?? []), function ($q) use ($shopField) {
                $q->whereIn('shop_id', $shopField ?? []);
            })
            ->where("withdraw_id", "=", null)
            ->where('is_paid', false)
            ->get();
        $payable = $parcels->sum('payable');

        $merchant_accounts = MerchantAccount::where('merchant_id', $id)
            ->where(function ($query) use ($user, $userType, $shopField) {
                $query->whereHas('parcel', function ($query) use ($user, $userType, $shopField) {
                    $query->when($userType === 'merchant_staff' && !in_array('all_parcel', $user->permissions ?? []), function ($inner) use ($shopField) {
                        $inner->whereIn('shop_id', $shopField ?? []);
                    });
                })
                    ->where(function ($query) {
                        $query->whereIn('source', ['previous_balance', 'cash_given_for_delivery_charge', 'parcel_return', 'paid_parcels_delivery_reverse', 'opening_balance'])
                            ->orWhere(function ($query) {
                                $query->where('source', 'vat_adjustment')
                                    ->whereIn('details', ['govt_vat_for_parcel_return', 'govt_vat_for_parcel_return_reversed']);
                            });
                    })
                    ->where('payment_withdraw_id', null)
                    ->where('is_paid', false);
            })
            ->get();

        $income = $merchant_accounts->where('type', 'income')->sum('amount');
        $expense = $merchant_accounts->where('type', 'expense')->sum('amount');

        $balance = $payable + $income - $expense;

        return $balance;
    }


    public function staffs()
    {
        return $this->hasMany(User::class)->where('user_type', 'merchant_staff');
    }

    public function defaultAccount()
    {
        return $this->hasOne(MerchantPaymentAccount::class, 'id', 'default_account_id');
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
