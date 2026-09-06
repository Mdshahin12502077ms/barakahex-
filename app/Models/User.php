<?php

namespace App\Models;

use App\Models\RoleUser;
use App\Enums\StatusEnum;
use App\Models\Account\Account;
use App\Models\Account\FundTransfer;
use Illuminate\Auth\Authenticatable;
use App\Models\Account\CompanyAccount;
use App\Models\Account\MerchantAccount;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as AuthenticatableContract;
use Illuminate\Contracts\Auth\Authenticatable as ContractAuthenticatable;

class User extends EloquentUser implements JWTSubject,ContractAuthenticatable
{
    use HasFactory, Notifiable, Authenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
        'dashboard',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions'       => 'array',
        'shops'             => 'array',
        'image_id'          => 'array',
        'status'            => StatusEnum::class,

    ];


    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }

    public static function byEmail($email){
        return static::whereEmail($email)->first();

    }

    public static function byPhoneNumber($phone_number){
        return static::wherePhoneNumber($phone_number)->first();

    }


    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }
    public function roleUser()
    {
        return $this->hasOne(RoleUser::class);
    }

    public function deliveryMan()
    {
        return $this->hasOne(DeliveryMan::class);
    }

    public function bankAccount()
    {
        return $this->hasOne(Account::class);
    }

    public function fromAccount()
    {
        return $this->hasManyThrough(FundTransfer::class, Account::class, 'user_id', 'from_account_id', 'id', 'id');
    }

    public function toAccount()
    {
        return $this->hasManyThrough(FundTransfer::class, Account::class, 'user_id', 'to_account_id', 'id', 'id');
    }

    public function companyAccount()
    {
        return $this->hasMany(CompanyAccount::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function accounts($id)
    {
        return $this->hasMany(Account::class)->where('user_id', $id)->get();
    }

    public function getJWTCustomClaims()
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }

    public function staffMerchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function balance($id)
    {
        $user = User::find($id);
        $parcels = Parcel::where('merchant_id', $user->merchant_id)
            ->where(function ($query) {
                $query->where('is_partially_delivered', '=', 1)
                    ->orWhereIn('status',['delivered','delivered-and-verified']);
            })
            ->when(!in_array('all_parcel_payment',$user->permissions), function ($q) use ($id){
                $q->where('user_id', $id);
            })
            ->where("withdraw_id", "=", null)
            ->where('is_paid',false)
            ->get();


        $payable   = $parcels->sum('payable');

        $merchant_accounts = MerchantAccount::where('merchant_id', $user->merchant_id)
            ->where(function ($query) use ($user){
                $query->whereHas('parcel',function ($query) use ($user){
                    $query->when(!in_array('all_parcel_payment', $user->permissions), function ($inner) use ($user){
                        $inner->where('user_id', $user->id);
                    });
                })
                ->whereIn('source', ['parcel_return','paid_parcels_delivery_reverse'])
                ->orWhere(function ($q){
                    $q->where('source','vat_adjustment')
                        ->whereIn('details',['govt_vat_for_parcel_return','govt_vat_for_parcel_return_reversed']);
                });
            })
            ->where('payment_withdraw_id', null)->where('is_paid',false)->get();

        $income = $merchant_accounts->where('type', 'income')->sum('amount');
        $expense = $merchant_accounts->where('type', 'expense')->sum('amount');

        $current_payable = $payable + $income - $expense;

        return $current_payable;
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
