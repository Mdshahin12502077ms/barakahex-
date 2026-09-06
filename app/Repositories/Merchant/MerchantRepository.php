<?php

namespace App\Repositories\Merchant;

use App\Models\Shop;
use App\Models\User;
use App\Models\Charge;
use App\Models\Branch;
use App\Models\Merchant;
use App\Enums\StatusEnum;
use App\Models\CodCharge;
use App\Models\TempStore;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Traits\SmsSenderTrait;
use App\Enums\PaymentMethodType;
use App\Traits\RandomStringTrait;
use App\Traits\RepoResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Image as ImageModel;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiReturnFormatTrait;
use App\Models\MerchantPaymentAccount;
use App\Models\Account\MerchantAccount;
use App\Traits\ImageTrait;
use App\Traits\SendMailTrait;
use Illuminate\Database\Eloquent\Builder;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use App\Traits\SendNotification;
use Illuminate\Support\Facades\Storage;

class MerchantRepository implements MerchantInterface
{

    use SmsSenderTrait;
    use RandomStringTrait;
    use ImageTrait;
    use ApiReturnFormatTrait, RepoResponseTrait, SendMailTrait, SendNotification;

    private $model;

    public function __construct(Merchant $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return Merchant::get();
    }

    public function activeAll()
    {
        return Merchant::where('status', 1)->get();
    }

    public function paginate($limit)
    {
        return Merchant::with('user', 'parcels')
            ->when(!hasPermission('read_all_merchant'), function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('branch_id', \Sentinel::getUser()->branch_id)
                        ->orWhere('branch_id', null);
                });
            })->orderByDesc('id')->paginate($limit);
    }

    public function get($id)
    {
        return Merchant::find($id);
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $image = [];
            $image = $request->image_id;

            if (isset($image)) {
                $response = $this->saveImage($image, 'image');
                $images = $response['images'];
            }

            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->password = bcrypt($request->password);
            $user->permissions = isset($request->permissions) ? $request->permissions : [];
            $user->user_type = 'merchant';
            $user->is_primary = 1;
            $user->image_id = $images ?? null;
            $user->save();

            $this->saveMerchant($request, $user->id);

            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);

            DB::commit();

            return $this->formatResponse(true, __('inserted_successfully'), 'merchant', []);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Merchant Store Repository Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            throw $e; // IMPORTANT: let controller decide response
        }
    }


    public function update($request)
    {
        DB::beginTransaction();
        try {
            $user = User::find($request->id);

            $image = [];
            $image = $request->image_id;
            if (isset($image)) {
                $response = $this->saveImage($image, 'image');
                $images = $response['images'];
            }

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->image_id = $images ?? $user->image_id;

            if ($request->password != ""):
                $user->password = bcrypt($request->password);
            endif;
            $user->permissions = isset($request->permissions) ? $request->permissions : [];
            $user->save();
            $this->updateMerchant($request);
            DB::commit();
            return $this->formatResponse(true, __('updated_successfully'), 'merchant', []);
        } catch (\Exception $e) {
            ;
            DB::rollback();
            Log::error('Merchant Update repo Error ' . $e->getMessage());
            return $this->formatResponse(false, __('something_went_wrong'), 'merchant', []);
        }
    }

    public function saveMerchant($request, $userId)
    {
        try {

            // -------- NID --------
            $nid = $request->nid;
            if (isset($nid)) {
                $response = $this->saveImage($nid, 'image');
                $nid = $response['images'];
            }

            // -------- Trade License --------
            $trade_license = $request->trade_license;
            if (isset($trade_license)) {
                $response = $this->saveImage($trade_license, 'image');
                $trade_license = $response['images'];
            }

            // -------- COD Charges --------
            $cod_charges = [];
            foreach ($request->locations as $key => $location) {
                $cod_charges[$location] = $request->charge[$key];
            }

            // -------- Charges --------
            $charges = [];
            foreach ($request->weights as $key => $weight) {
                $charges[$weight] = [
                    'same_day' => $request->same_day[$key],
                    'sub_city' => $request->sub_city[$key],
                    'sub_urban_area' => $request->sub_urban_area[$key],
                ];
            }

            // -------- Merchant --------
            $merchant = new Merchant();
            $merchant->user_id = $userId;
            $merchant->company = $request['company'];
            $merchant->vat = $request['vat'] == '' ? 0.00 : $request['vat'];
            $merchant->phone_number = $request['phone_number'];
            $merchant->city = $request['city'];
            $merchant->zip = $request['zip'];
            $merchant->address = $request['address'];
            $merchant->website = $request['website'];
            $merchant->billing_street = $request['billing_street'];
            $merchant->billing_city = $request['billing_city'];
            $merchant->billing_zip = $request['billing_zip'];
            $merchant->nid = $nid ?? null;
            $merchant->trade_license = $trade_license ?? null;
            $merchant->api_key = $this->generate_random_string(15);
            $merchant->secret_key = $this->generate_random_string(30);
            $merchant->cod_charges = $cod_charges;
            $merchant->charges = $charges;
            $merchant->save();

            // -------- Merchant Account --------
            $merchant_account = new MerchantAccount();
            $merchant_account->details = 'opening_balance';
            $merchant_account->source = 'opening_balance';
            $merchant_account->date = date('Y-m-d');
            $merchant_account->type = 'income';
            $merchant_account->amount = $request['opening_balance'];
            $merchant_account->merchant_id = $merchant->id;
            $merchant_account->save();

            // -------- Other Related Data --------
            $this->saveMerchantPaymentAccount($merchant->id);
            $this->saveMerchantShop($merchant->id, $request);
        } catch (\Throwable $e) {

            Log::error('Save Merchant Failed', [
                'user_id' => $userId,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            throw $e; // VERY IMPORTANT
        }
    }




    public function updateMerchant($request)
    {


        $cod_charges = [];
        foreach ($request->locations as $key => $location) {
            $cod_charges[$location] = $request->charge[$key];
        }

        $charges = [];
        foreach ($request->weights as $key => $weight) {
            $charges[$weight] = [
                'same_day' => $request->same_day[$key],
                // 'next_day' => $request->next_day[$key],
                'sub_city' => $request->sub_city[$key],
                'sub_urban_area' => $request->sub_urban_area[$key],
            ];
        }

        $merchant = Merchant::find($request->merchant_id);

        $merchant->company = $request['company'];
        $merchant->vat = $request['vat'] == '' ? 0.00 : $request['vat'];
        $merchant->phone_number = $request['phone_number'];
        $merchant->city = $request['city'];
        $merchant->zip = $request['zip'];
        $merchant->address = $request['address'];
        $merchant->website = $request['website'];
        $merchant->billing_street = $request['billing_street'];
        $merchant->billing_city = $request['billing_city'];
        $merchant->billing_zip = $request['billing_zip'];



        if (isset($request['trade_license'])) {
            $response = $this->saveImage($request['trade_license'], 'image');
            $merchant->trade_license = $response['images'] ?? $merchant->trade_license;
        }


        if (isset($request['nid'])) {
            $response = $this->saveImage($request['nid'], 'image');
            $merchant->nid = $response['images'] ?? $merchant->nid;
        }

        $merchant->cod_charges = $cod_charges;
        $merchant->charges = $charges;

        $merchant->save();

        if ($merchant->merchantAccount) {
            if ($merchant->merchantAccount->payment_withdraw_id == null && $merchant->merchantAccount->is_paid == false):
                $merchant_account = $merchant->merchantAccount;
                $merchant_account->details = 'opening_balance';
                $merchant_account->source = 'opening_balance';
                $merchant_account->date = date('Y-m-d');
                $merchant_account->type = 'income';
                $merchant_account->amount = $request['opening_balance'];
                $merchant_account->save();
            endif;
        }
    }

    public function fileUpload($image, $type)
    {
        $requestImage = $image;
        $fileType = $requestImage->getClientOriginalExtension();
        $original = date('YmdHis') . '-' . $type . rand(1, 50) . '.' . $fileType;
        $directory = 'admin/' . $type . '/';  // Relative path
        $storagePath = public_path($directory);
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        $originalFilePath = $storagePath . $original;
        if ($fileType == 'pdf') {
            $requestImage->move($storagePath, $original);
        } else {
            Image::make($requestImage)->save($originalFilePath, 80);
        }
        return static_asset($directory . $original);
    }


    public function delete($id, $merchant)
    {
        DB::beginTransaction();
        try {

            $user = User::find($id);
            $image = ImageModel::find($user->image_id);
            if (!blank($image)):
                if ($image->original_image != "" && file_exists($image->original_image)):
                    unlink($image->original_image);
                endif;
                if ($image->image_small_one != "" && file_exists($image->image_small_one)):
                    unlink($image->image_small_one);
                endif;
                if ($image->image_small_two != "" && file_exists($image->image_small_two)):
                    unlink($image->image_small_two);
                endif;
                if ($image->image_small_three != "" && file_exists($image->image_small_three)):
                    unlink($image->image_small_three);
                endif;
                $image->delete();
            else:
                $user->delete();
            endif;

            if (!blank($merchant->nid)):
                if ($merchant->nid != "" && file_exists($merchant->nid)):
                    unlink($merchant->nid);
                endif;
            endif;

            if (!blank($merchant->trade_license)):
                if ($merchant->trade_license != "" && file_exists($merchant->trade_license)):
                    unlink($merchant->trade_license);
                endif;
            endif;

            MerchantAccount::where('merchant_id', $merchant->id)->where('source', 'opening_balance')->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function removeOldFile($image)
    {
        if ($image != "" && file_exists($image)):
            unlink($image);
        endif;
    }

    public function filter($request)
    {

        $query = Merchant::query();

        if (!hasPermission('read_all_merchant')) {
            $query->whereHas('user', function ($q) {
                $q->where('branch_id', Sentinel::getUser()->branch_id)
                    ->orWhere('branch_id', null);
            });
        }

        if ($request->branch != 'all') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->when($request->branch == 'pending', function ($search) {
                    $search->where('branch_id', null);
                })->when($request->branch != 'pending', function ($search) use ($request) {
                    $search->where('branch_id', $request->branch);
                });
            });
        }

        if ($request->company_name != "") {
            $query->where('company', 'LIKE', "%{$request->company_name}%");
        }

        if ($request->approval_status != "any") {
            $query->where('registration_confirmed', $request->approval_status);
        }

        if ($request->status != "any") {
            $query->where('status', $request->status);
        }

        if ($request->sort_by == 'oldest_on_top'):
            $query->orderBy('id');
        elseif ($request->sort_by == 'newest_on_top'):
            $query->orderByDesc('id');
        else:
            $query->withCount([
                'parcels' => function (Builder $query) {
                    $query->where(function ($query) {
                        $query->whereIn('parcels.status', ['delivered', 'delivered-and-verified'])
                            ->orWhere('is_partially_delivered', true);
                    });
                }
            ])->orderBy('parcels_count', 'desc')->orderByDesc('id');
        endif;

        return $query->paginate(\Config::get('parcel.parcel_merchant_paginate'));
    }

    public function statusChange($request)
    {
        try {
            DB::beginTransaction();

            $row = $this->model->find($request->id);
            if (!$row) {
                return false;
            }
            if ($row->status == StatusEnum::ACTIVE) {
                $row->status = StatusEnum::INACTIVE;
            } elseif ($row->status == StatusEnum::INACTIVE) {
                $row->status = StatusEnum::ACTIVE;
            }
            $row->save();

            $user = User::where('id', $row->user_id)->first();
            if ($user->status == StatusEnum::ACTIVE) {
                $user->status = StatusEnum::INACTIVE;
            } elseif ($user->status == StatusEnum::INACTIVE) {
                $user->status = StatusEnum::ACTIVE;
            }
            $user->save();

            DB::commit();
            return $this->responseWithSuccess(__('updated_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->responseWithError($th->getMessage(), []);
        }
    }



    public function changeDefault($request)
    {
        try {
            $shop = Shop::find($request['shop_id']);
            $merchant = $this->get($shop->merchant_id);
            $old_default = $merchant->shops()->where('default', 1)->first();
            if (!blank($old_default)):
                $old_default->default = 0;
                $old_default->save();
            endif;
            $shop->default = 1;
            $shop->save();
            return $this->responseWithSuccess('updated successfully', []);
        } catch (\Throwable $e) {
            return $this->responseWithError($e->getMessage(), []);
        }
    }

    public function tempStore($data)
    {
        DB::beginTransaction();
        try {
            $temp = new TempStore();

            $data['phone_number'] = preg_replace('/^(\+88|88|-)/', '', $data['phone_number']);
            $temp->company = $data['company'];
            $temp->first_name = $data['first_name'];
            $temp->last_name = $data['last_name'];
            $temp->address = $data['address'];
            $temp->phone_number = $data['phone_number'];
            $temp->email = $data['email'];
            $temp->password = bcrypt($data['password']);
            $temp->otp = rand(10000, 99999);
            $temp->ip = \Request::ip();
            $temp->browser = $this->getBrowser(\Request::header('user-agent'));
            $temp->platform = $this->getPlatForm(\Request::header('user-agent'));
            $temp->user_agent = \Request::header('user-agent');

            $sms_body = __('hello') . ' ' . $temp->first_name . ', ' . __('use') . ' ' . $temp->otp . ' ' . __('to_verify_your_phone_number_on') . ' ' . setting('company_name');

            if ($result = $this->test($sms_body, $temp->phone_number, 'otp', setting('active_sms_provider'))):
                $temp->save();
                DB::commit();
                return ['temp_id' => $temp->id, 'otp' => $temp->otp];
            else:
                return 'false';
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function getPlatForm($u_agent)
    {
        $platform = '';
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        return $platform;
    }
    public function getBrowser($u_agent)
    {
        $bname = '';
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/OPR/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Chrome/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        } elseif (preg_match('/Edge/i', $u_agent)) {
            $bname = 'Edge';
            $ub = "Edge";
        } elseif (preg_match('/Trident/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        return $bname;
    }

    public function otpConfirm($request)
    {
        DB::beginTransaction();
        try {
            $temp = TempStore::find($request['id']);

            if ($temp->otp != $request['otp']):
                return false;
            endif;
            $user = new User();
            $user->first_name = $temp->first_name;
            $user->last_name = $temp->last_name;
            $user->email = $temp->email;
            $user->phone_number = $temp->phone_number;
            $user->password = $temp->password;
            $user->permissions = [];
            $user->user_type = 'merchant';
            $user->is_primary = 1;
            $user->branch_id = 1;

            $user->save();
            $data = $this->registerMerchant($temp, $user->id);

            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);

            $data = [
                'subject' => "email_confirmation",
                'user' => $user,
                'reset_link' => url('/') . '/activation/' . $user->email . '/verify',
                'template_title' => 'email_confirmation',
            ];


            try {
                $this->sendMail($user->email, 'merchant.auth.mail.activate-account-email', $data);
            } catch (\Exception $e) {
                \Log::info($e->getMessage());
            }


            $temp->delete();

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            if (str_contains($e->getMessage(), 'Default branch')) {
                throw $e;
            }
            return false;
        }
    }

    public function resendOtp($id)
    {
        DB::beginTransaction();
        try {
            $temp = TempStore::find($id);
            $temp->otp = rand(10000, 99999);

            $sms_body = __('hello') . ' ' . $temp->first_name . ', ' . __('use') . ' ' . $temp->otp . ' ' . __('to_verify_your_phone_number_on_deliX_ecourier');

            $this->test($sms_body, $temp->phone_number, 'resend-otp', setting('active_sms_provider'));

            $temp->save();

            DB::commit();
            return $temp->id;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function registerWithoutVerification($data)
    {
        DB::beginTransaction();
        try {
            $data['phone_number'] = preg_replace('/^(\+88|88|-)/', '', $data['phone_number']);

            $user = new User();
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->email = $data['email'];
            $user->phone_number = $data['phone_number'];
            $user->password = bcrypt($data['password']);
            $user->permissions = [];
            $user->user_type = 'merchant';
            $user->is_primary = 1;
            $user->branch_id = 1;
            $user->save();

            // Create a temporary array to mimic TempStore structure for reusability of registerMerchant
            $temp = [];
            $temp['company'] = $data['company'];
            $temp['phone_number'] = $data['phone_number'];
            $temp['address'] = $data['address'];

            $this->registerMerchant($temp, $user->id);

            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);

            // Send Notifications
            $users = [];
            $details = __('new_merchant_registered') . '-' . $user->merchant->company;
            $users = User::where('user_type', 'staff')->get();
            $permissions = ['merchant_read'];
            $title = __('new_merchant_registered') . '-' . $user->merchant->company;
            $this->sendNotification($title, $users, $details, $permissions, 'success', url('admin/merchant/personal-info/' . $user->merchant->id), '');

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e->getMessage());
            if (str_contains($e->getMessage(), 'Default branch')) {
                throw $e;
            }
            return false;
        }
    }

    public function registerMerchant($data, $user_id)
    {
        try {
            $cod_charges = CodCharge::all();
            $charges = Charge::all();

            $array_cod_charges = [];
            foreach ($cod_charges as $cod_charge) {
                $array_cod_charges[$cod_charge->location] = $cod_charge->charge;
            }

            $array_charges = [];
            foreach ($charges as $charge) {
                $array_charges[$charge->weight] = [
                    'same_day' => $charge->same_day,
                    'sub_city' => $charge->sub_city,
                    'sub_urban_area' => $charge->sub_urban_area,
                ];
            }

            $merchant = new Merchant();
            $merchant->user_id = $user_id;
            $merchant->company = $data['company'];
            $merchant->phone_number = $data['phone_number'];
            $merchant->address = $data['address'];
            $merchant->vat = 0.00;
            $merchant->api_key = $this->generate_random_string(15);
            $merchant->secret_key = $this->generate_random_string(30);
            $merchant->cod_charges = $array_cod_charges;
            $merchant->charges = $array_charges;
            $merchant->save();

            $merchant_account = new MerchantAccount();
            $merchant_account->details = 'opening_balance';
            $merchant_account->source = 'opening_balance';
            $merchant_account->date = date('Y-m-d');
            $merchant_account->type = 'income';
            $merchant_account->amount = 0.00;
            $merchant_account->merchant_id = $merchant->id;
            $merchant_account->save();

            $this->saveMerchantPaymentAccount($merchant->id);
            $this->saveMerchantShop($merchant->id, $data);
        } catch (\Throwable $e) {

            Log::error('Register Merchant Error', [
                'user_id' => $user_id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'payload' => $data,
            ]);

            throw $e;
        }
    }




    public function saveMerchantPaymentAccount($merchant_id)
    {
        try {
            $payment_account = new MerchantPaymentAccount();
            $payment_account->merchant_id = $merchant_id;
            $payment_account->save();
        } catch (\Throwable $e) {

            Log::error('Merchant Payment Account Creation Failed', [
                'merchant_id' => $merchant_id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }


    public function saveMerchantShop($merchant_id, $request)
    {
        try {
            $default_branch = Branch::where('default', 1)->first();

            if (!$default_branch) {
                throw new \Exception('Default branch not found.');
            }

            $merchant_shop = new Shop();
            $merchant_shop->merchant_id = $merchant_id;
            $merchant_shop->shop_name = $request['company'];
            $merchant_shop->contact_number = $request['phone_number'];
            $merchant_shop->pickup_branch_id = $request['pickup_branch'] ?? $default_branch->id;
            $merchant_shop->shop_phone_number = $request['phone_number'];
            $merchant_shop->address = $request['address'];
            $merchant_shop->default = 1;
            $merchant_shop->save();
        } catch (\Throwable $e) {

            Log::error('Merchant Shop Creation Failed', [
                'merchant_id' => $merchant_id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'payload' => $request,
            ]);

            throw $e;
        }
    }

    public function updateMerchantByMerchant($request)
    {
        DB::beginTransaction();
        try {
            $merchant = Merchant::find($request->merchant);

            $user = $merchant->user;
            $user->phone_number = $request['phone_number'];
            $user->save();

            $merchant->company = $request['company'];
            $merchant->phone_number = $request['phone_number'];
            $merchant->city = $request['city'];
            $merchant->zip = $request['zip'];
            $merchant->address = $request['address'];
            $merchant->website = $request['website'];
            $merchant->billing_street = $request['billing_street'];
            $merchant->billing_city = $request['billing_city'];
            $merchant->billing_zip = $request['billing_zip'];

            if (isset($request['trade_license'])) {
                $response = $this->saveImage($request['trade_license'], 'image');
                $merchant->trade_license = $response['images'] ?? $merchant->trade_license;
            }


            if (isset($request['nid'])) {
                $response = $this->saveImage($request['nid'], 'image');
                $merchant->nid = $response['images'] ?? $merchant->nid;
            }

            $merchant->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    public function shopStore($request)
    {
        DB::beginTransaction();
        try {
            $shop = new Shop();
            $shop->merchant_id = $request->merchant;
            $shop->shop_name = $request->shop_name;
            $shop->pickup_branch_id = $request->pickup_branch;
            $shop->contact_number = $request->contact_number;
            $shop->shop_phone_number = $request->shop_phone_number;
            $shop->address = $request->address;
            $shop->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function shopUpdate($request)
    {
        DB::beginTransaction();
        try {
            $shop = Shop::find($request->shop);
            $shop->shop_name = $request->shop_name;
            $shop->pickup_branch_id = $request->pickup_branch;
            $shop->contact_number = $request->contact_number;
            $shop->shop_phone_number = $request->shop_phone_number;
            $shop->address = $request->address;
            $shop->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function paymentAccount($id)
    {
        try {
            $merchant = $this->get($id);
            $payment_account = $merchant->paymentAccount;

            return $payment_account;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateBankDetails($request)
    {

        DB::beginTransaction();
        try {
            $merchant = $this->get($request->merchant_id);
            $payment_account = MerchantPaymentAccount::where('type', PaymentMethodType::BANK->value)->where('merchant_id', $merchant->id)->first();
            $type = PaymentMethod::where('id', $request->method_id)->first()->type;
            if (!$payment_account) {
                $payment_account = new MerchantPaymentAccount;
                $payment_account->merchant_id = $merchant->id;
            }
            $payment_account->payment_method_id = $request->method_id;
            $payment_account->bank_branch = $request->bank_branch;
            $payment_account->bank_ac_name = $request->bank_ac_name;
            $payment_account->bank_ac_number = $request->bank_ac_number;
            $payment_account->routing_no = $request->routing_no;
            $payment_account->type = $type;
            $payment_account->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return false;
        }
    }

    public function updateOthersAccountDetails($request)
    {
        DB::beginTransaction();
        try {
            $merchant = $request->merchant_id;

            $numbersCount = count($request->mfs_number);
            for ($key = 0; $key < $numbersCount; $key++) {
                if (isset($request->mfs_number[$key])) {
                    $payment_account = MerchantPaymentAccount::where('merchant_id', $merchant)
                        ->where('payment_method_id', $request->payment_method_id[$key])
                        ->first();
                    $type = PaymentMethod::where('id', $request->payment_method_id[$key])->first()->type;
                    if (!$payment_account) {
                        $payment_account = new MerchantPaymentAccount;
                        $payment_account->merchant_id = $merchant;
                        $payment_account->payment_method_id = $request->payment_method_id[$key];
                    }
                    $payment_account->mfs_number = $request->mfs_number[$key];
                    $payment_account->mfs_ac_type = $request->mfs_ac_type[$key] ?? 'personal';
                    $payment_account->type = $type;
                    $payment_account->save();
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function apiCredentialsUpdate($request)
    {
        DB::beginTransaction();
        try {
            $merchant = $this->get($request->id);

            $merchant->api_key = $request->api_key;
            $merchant->secret_key = $request->secret_key;
            if ($request->has('webhook_url')) {
                $merchant->webhook_url = $request->webhook_url;
            }
            if ($request->has('webhook_secret')) {
                $merchant->webhook_secret = $request->webhook_secret;
            }
            $merchant->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function permissionUpdate($request, $merchant)
    {
        DB::beginTransaction();
        try {
            $user = $merchant->user;
            $user->permissions = isset($request->permissions) ? $request->permissions : [];
            $user->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::Rollback();
            return false;
        }
    }
}
