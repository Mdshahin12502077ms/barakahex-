<?php

namespace App\Repositories;

use App\Models\Charge;
use App\Models\CustomerParcelSmsTemplates;
use App\Models\DeliveryMan;
use App\Models\DistrictZila;
use App\Models\Merchant;
use App\Models\Parcel;
use App\Models\ParcelEvent;
use App\Models\PercelMovement;
use App\Models\PercelOtpLog;
use App\Models\SmsTemplate;
use App\Models\ThirdParty;
use App\Models\User;
use App\Repositories\Interfaces\AccountInterface;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use App\Repositories\Interfaces\ParcelInterface;
use App\Traits\PaperFlyParcel;
use App\Traits\SendNotification;
use App\Traits\ShortenLinkTrait;
use App\Traits\SmsSenderTrait;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Image;

class ParcelRepository implements ParcelInterface
{

    use SmsSenderTrait, ShortenLinkTrait, PaperFlyParcel, SendNotification;

    protected $merchants;
    protected $accounts;

    public function __construct(MerchantInterface $merchants, AccountInterface $accounts)
    {
        $this->merchants = $merchants;
        $this->accounts = $accounts;
    }

    public function all()
    {
        return Parcel::all();
    }

    public function paginate($limit)
    {
        return Parcel::orderBy('id', 'desc')
            ->when(!hasPermission('read_all_parcel'), function ($query) {
                $query->where('branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhere('pickup_branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhereNull('pickup_branch_id')
                    ->orWhere('transfer_to_branch_id', \Sentinel::getUser()->branch_id);
            })
            ->paginate($limit);
    }

    public function get($id)
    {
        return Parcel::find($id);
    }

    public function getMerchants()
    {
        return Merchant::all();
    }

    public function getDeliveryMan()
    {
        return DeliveryMan::all();
    }

    public function chargeDetails($request)
    {
   
       $packaging_charge = number_format(0, 2);
    if ($request->packaging != 'no') {
        $packaging_charge = settingHelper('package_and_charges')->where('id', $request->packaging)->first()->charge;
    }

    $fragile_charge = number_format(0, 2);
    if ($request->fragile == 1) {
        $fragile_charge = settingHelper('fragile_charge');
    }

    $cod = number_format(0, 2);
    if ($request->cod != "") {
        $cod = $request->cod;
    }

    if ($request->merchant == "" || $request->weight == "" || $request->parcel_type == "") {
        $data['charge'] = number_format(0, 2);
        $data['cod_charge'] = number_format(0, 2);
        $data['vat'] = number_format(0, 2);
        $data['cod'] = number_format($cod, 2);
        $data['total_delivery_charge'] = $data['charge'] + $data['cod_charge'] + $packaging_charge + $fragile_charge;
        $data['vat'] = number_format($data['total_delivery_charge'] / 100 * $data['vat'], 2);
        $data['total_delivery_charge'] += $data['vat'];
        $data['payable'] = number_format($cod - $data['total_delivery_charge'], 2);
    } else {
        $parcelType = $request->parcel_type == "outside_city" ? "sub_urban_area" : $request->parcel_type;

        if ($request->parcel_type == "same_day" || $request->parcel_type == "next_day" || $request->parcel_type == "frozen"):
            $location = 'inside_city';
        elseif ($request->parcel_type == "sub_city"):
            $location = 'sub_city';
        elseif ($request->parcel_type == "outside_city"):
            $location = 'sub_urban_area';
        elseif ($request->parcel_type == "third_party_booking"):
            $location = 'third_party_booking';
        endif;

        $merchant = $this->merchants->get($request->merchant);
        $requestWeight = (float) $request->weight;

        // --- system charge: bracket match (smallest tier >= requested weight) ---
        $system_charge = Charge::all()->sortBy('weight')->values()->toArray();
        $foundCharge = null;
        foreach ($system_charge as $charge) {
            if ($requestWeight <= (float) $charge['weight'] && isset($charge[$parcelType])) {
                $foundCharge = $charge[$parcelType];
                break;
            }
        }
        // fallback: weight exceeds all defined tiers -> use highest tier
        if ($foundCharge === null && count($system_charge)) {
            $last = end($system_charge);
            if (isset($last[$parcelType])) {
                $foundCharge = $last[$parcelType];
            }
        }

        // --- merchant-specific override: bracket match ---
        $merchantCharge = null;
        if (!empty($merchant->charges)) {
            $merchantCharges = collect($merchant->charges)
                ->sortKeys(SORT_NUMERIC)
                ->toArray();

            foreach ($merchantCharges as $weightKey => $ratesByType) {
                if ($requestWeight <= (float) $weightKey && isset($ratesByType[$parcelType])) {
                    $merchantCharge = $ratesByType[$parcelType];
                    break;
                }
            }
            // fallback: weight exceeds all merchant-defined brackets -> use highest bracket
            if ($merchantCharge === null) {
                $lastKey = array_key_last($merchantCharges);
                if ($lastKey !== null && isset($merchantCharges[$lastKey][$parcelType])) {
                    $merchantCharge = $merchantCharges[$lastKey][$parcelType];
                }
            }
        }

        $data['charge'] = $merchantCharge ?? $foundCharge ?? 0;

        $data['cod_charge'] = data_get($merchant->cod_charges, $location);
        $data['vat'] = $merchant->vat ?? 0.00;
        $data['cod_charge'] = floor($cod / 100 * $data['cod_charge']);
        $data['total_delivery_charge'] = $data['charge'] + $data['cod_charge'] + $packaging_charge + $fragile_charge;
        $data['vat'] = floor($data['total_delivery_charge'] / 100 * $data['vat']);
        $data['total_delivery_charge'] += $data['vat'];
        $data['charge'] = number_format($data['charge'], 2);
        $data['cod_charge'] = number_format($data['cod_charge'], 2);
        $data['vat'] = number_format($data['vat'], 2);
        $data['cod'] = number_format($cod, 2);
        $data['payable'] = number_format(ceil($cod - $data['total_delivery_charge']), 2);
    }

    $data['packaging_charge'] = number_format($packaging_charge, 2);
    $data['fragile_charge'] = number_format($fragile_charge, 2);

    return $data;
    }

    public function store($request)
    {
        
       DB::beginTransaction();
try {

    $fragile_charge = number_format(0, 2);
    $fragile = 0;

    $packaging_charge = number_format(0, 2);
    $packaging = 'no';

    if (isset($request->fragile)) {
        $fragile = 1;
        $fragile_charge = (float) settingHelper('fragile_charge');
    }

    if (!empty($request->packaging) && $request->packaging != 'no') {
        $packageSetting = settingHelper('package_and_charges')->where('id', $request->packaging)->first();
        if ($packageSetting) {
            $packaging = $request->packaging;
            $packaging_charge = (float) $packageSetting->charge;
        }
    }
    $parcelType = $request->parcel_type == "outside_city" ? "sub_urban_area" : $request->parcel_type;

    // cod charge define by location
    if ($request->parcel_type == "same_day" || $request->parcel_type == "next_day" || $request->parcel_type == "frozen"):
        $location = 'inside_city';
    elseif ($request->parcel_type == "sub_city"):
        $location = 'sub_city';
    elseif ($request->parcel_type == "outside_city"):
        $location = 'sub_urban_area';
    elseif ($request->parcel_type == "third_party_booking"):
        $location = 'third_party_booking';
    else:
        $location = null; // unexpected parcel_type — avoids undefined variable notice below
    endif;

    // Start Charge calculate
    $merchant = $this->merchants->get($request->merchant);
    $requestWeight = (float) $request->weight;

    // --- system charge: bracket match (smallest defined weight >= requested weight) ---
    $system_charge = Charge::all()->sortBy('weight')->values()->toArray();
    $foundCharge = null;
    foreach ($system_charge as $sysCharge) {
        if ($requestWeight <= (float) $sysCharge['weight'] && isset($sysCharge[$parcelType])) {
            $foundCharge = $sysCharge[$parcelType];
            break;
        }
    }
    // fallback: weight exceeds every defined tier -> use the highest tier
    if ($foundCharge === null && count($system_charge)) {
        $last = end($system_charge);
        if (isset($last[$parcelType])) {
            $foundCharge = $last[$parcelType];
        }
    }

    // --- merchant-specific override: bracket match ---
    $merchantCharge = null;
    if (!empty($merchant->charges)) {
        $merchantCharges = collect($merchant->charges)->sortKeys(SORT_NUMERIC)->toArray();
        foreach ($merchantCharges as $weightKey => $ratesByType) {
            if ($requestWeight <= (float) $weightKey && isset($ratesByType[$parcelType])) {
                $merchantCharge = $ratesByType[$parcelType];
                break;
            }
        }
        // fallback: weight exceeds every merchant-defined bracket -> use the highest one
        if ($merchantCharge === null) {
            $lastKey = array_key_last($merchantCharges);
            if ($lastKey !== null && isset($merchantCharges[$lastKey][$parcelType])) {
                $merchantCharge = $merchantCharges[$lastKey][$parcelType];
            }
        }
    }

    $charge = (float) ($merchantCharge ?? $foundCharge ?? 0);
    // End Charge calculate

    $cod_charge = (float) data_get($merchant->cod_charges, $location);

    $vat = $merchant->vat ?? 0.00;

    $total_delivery_charge = $charge + $packaging_charge + $fragile_charge + ($request->price / 100 * $cod_charge);

    $total_delivery_charge += $total_delivery_charge / 100 * $vat;

    $payable = $request->price - $total_delivery_charge;
    $unsafeChars = ['=', '+', '-', '@'];

    $parcel = new Parcel();
    $parcel->parcel_no = make_unique_parcel_id();
    $parcel->short_url = url('/tracking/' . $parcel->parcel_no);
    $parcel->merchant_id = $request->merchant;
    $parcel->price = $request->price;
    $parcel->selling_price = $request->selling_price ?? 0;
    $parcel->customer_name = ltrim($request->customer_name, implode('', $unsafeChars));
    $parcel->customer_invoice_no = $request->customer_invoice_no ?? (function () use ($merchant) {
        do {
            $inv = 'inv-' . $merchant->id . rand(1000, 9999);
        } while (Parcel::where('customer_invoice_no', $inv)->exists());
        return $inv;
    })();
    $parcel->customer_phone_number = ltrim($request->customer_phone_number, implode('', $unsafeChars));
    $parcel->customer_address = ltrim($request->customer_address, implode('', $unsafeChars));
    $parcel->district_id = $request->district_id ?? $request->city_id ?? null;
    $parcel->thana_id = $request->thana_id ?? null;
    $parcel->note = ltrim($request->note, implode('', $unsafeChars));

    // Charge
    $parcel->packaging = $packaging;
    $parcel->packaging_charge = $packaging_charge;
    $parcel->fragile = $fragile;
    $parcel->open_box = $request->open_box ?? 0;
    $parcel->home_delivery = $request->home_delivery ?? 0;
    $parcel->fragile_charge = $fragile_charge;

    $parcel->weight = $request->weight;
    $parcel->parcel_type = $parcelType;
    $parcel->charge = $charge;
    $parcel->cod_charge = $cod_charge;
    $parcel->vat = $vat;
    $parcel->total_delivery_charge = floor($total_delivery_charge);
    $parcel->payable = ceil($payable);
    $parcel->location = $location;
    // End charge

    // pickup shop details
    $parcel->pickup_shop_phone_number = $request->shop_phone_number;
    $parcel->pickup_address = $request->shop_address;

    if ($request->has('shop') && $request->shop != '') {
        $shop = $merchant->shops->where('id', $request->shop)->first();
        if ($shop->pickup_branch_id) {
            $parcel->pickup_branch_id = $shop->pickup_branch_id;
        } else {
            $defaultShop = $merchant->shops->where('default', true)->first();
            $parcel->pickup_branch_id = $defaultShop->pickup_branch_id;
        }
    }

    $parcel->shop_id = $request->shop != '' ? $request->shop : ($merchant->shops->where('default', true)->first() ? $merchant->shops->where('default', true)->first()->id : null);
    $parcel->user_id = $request->created_by != "" ? $request->created_by : (Sentinel::getUser() ? Sentinel::getUser()->id : ($merchant->user_id ?? 1));

    $destBranchId = $request->destination_branch_id ?? $request->transfer_branch_select_id;
    if ($request->has('transfer_to_branch') && !empty($destBranchId)) {
        $parcel->destination_branch_id = $destBranchId;
    }
    if ($request->parcel_type == 'frozen') {

        $pickup_date = date('Y-m-d');
        $pickup_time = date('h:i:s');
        $delivery_date = date("Y-m-d", strtotime('+2 hours', strtotime($pickup_date)));
        $delivery_time = date("h:i:s", strtotime('+2 hours', strtotime($pickup_time)));
    } elseif ($request->parcel_type == 'same_day') {
        if (date('H') >= settingHelper('pickup_accept_start') && date('H') <= settingHelper('pickup_accept_end')) {
            $pickup_date = date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d'))));
            $delivery_date = date("Y-m-d", strtotime('+1 days', strtotime(date('Y-m-d'))));
        } else {
            $pickup_date = date('Y-m-d');
            $delivery_date = date("Y-m-d");
        }
    } elseif ($request->parcel_type == 'outside_city') {

        if (date('H') >= settingHelper('pickup_accept_start') && date('H') <= settingHelper('pickup_accept_end')) {

            $days = settingHelper('outside_dhaka_days') + 1;

            $pickup_date = date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d'))));
            $delivery_date = date("Y-m-d", strtotime('+' . $days . ' days', strtotime(date('Y-m-d'))));
        } else {

            $days = settingHelper('outside_dhaka_days');

            $pickup_date = date('Y-m-d');
            $delivery_date = date("Y-m-d", strtotime('+' . $days . ' days', strtotime(date('Y-m-d'))));
        }
    } else {

        if (date('H') > settingHelper('pickup_accept_start') && date('H') <= settingHelper('pickup_accept_end')) {
            $pickup_date = date('Y-m-d');
            $delivery_date = date("Y-m-d", strtotime('+2 days', strtotime(date('Y-m-d'))));
        } else {
            $pickup_date = date('Y-m-d');
            $delivery_date = date("Y-m-d", strtotime('+1 days', strtotime(date('Y-m-d'))));
        }
    }

    $parcel->pickup_date = $pickup_date;

    $parcel->date = date('Y-m-d');

    if (isset($pickup_time)):
        $parcel->pickup_time = $pickup_time ?? '';
    endif;
    $parcel->delivery_date = $delivery_date;
    if (isset($delivery_time)):
        $parcel->delivery_time = $delivery_time ?? '';
    endif;
    $parcel->save();

    $this->parcelEvent($parcel->id, 'parcel_create_event');

    DB::commit();
    return $parcel;
} catch (\Exception $e) {
    Log::error('Parcel Store Repo Error : ' . $e->getMessage());
    DB::rollback();
    throw $e;
}
    }

    public function update($request)
    {
        DB::beginTransaction();
        try {

            $fragile_charge = number_format(0, 2);
            $fragile = 0;

            $packaging_charge = number_format(0, 2);
            $packaging = 'no';

            if (isset($request->fragile)) {
                $fragile = 1;
                $fragile_charge = settingHelper('fragile_charge');
            }

            if ($request->packaging != 'no') {
                $packaging = $request->packaging;
                $packaging_charge = settingHelper('package_and_charges')->where('id', $request->packaging)->first()->charge;
            }
            $parcelType = $request->parcel_type == "outside_city" ? "sub_urban_area" : $request->parcel_type;

            if ($request->parcel_type == "same_day" || $request->parcel_type == "next_day" || $request->parcel_type == "frozen"):
                $location = 'inside_city';
            elseif ($request->parcel_type == "sub_city"):
                $location = 'sub_city';
            elseif ($request->parcel_type == "outside_city"):
                $location = 'sub_urban_area';
            elseif ($request->parcel_type == "third_party_booking"):
                $location = 'third_party_booking';
            endif;

            // Start Charge calculate
            $merchant = $this->merchants->get($request->merchant);
            $system_charge = Charge::all()->toArray();
            $foundCharge = null;
            foreach ($system_charge as $charge) {
                if ($charge['weight'] == $request->weight && isset($charge[$parcelType])) {
                    $foundCharge = $charge[$parcelType];
                    break;
                }
            }

            if (data_get($merchant->charges, $request->weight . '.' . $parcelType)) {
                $charge = data_get($merchant->charges, $request->weight . '.' . $parcelType);
            } else {
                $charge = $foundCharge;
            }

            $cod_charge = data_get($merchant->cod_charges, $location);
            $vat = $merchant->vat ?? 0.00;
            $total_delivery_charge = $charge + $packaging_charge + $fragile_charge + ($request->price / 100 * $cod_charge);
            $total_delivery_charge += $total_delivery_charge / 100 * $vat;
            $adjustment = $request->adjustment ?? 0;
            $payable = $request->price - $total_delivery_charge + $adjustment;
            $unsafeChars = ['=', '+', '-', '@'];


            // End charge calculate
            $parcel = Parcel::find($request->id);
            $parcel->merchant_id = $request->merchant;
            $parcel->price = $request->price;
            $parcel->selling_price = $request->selling_price ?? 0;
            $parcel->customer_name = ltrim($request->customer_name, implode('', $unsafeChars));
            $parcel->customer_invoice_no = $request->customer_invoice_no ?? (function () use ($merchant) {
                do {
                    $inv = 'inv-' . $merchant->id . rand(1000, 9999);
                } while (Parcel::where('customer_invoice_no', $inv)->exists());
                return $inv;
            })();
            $parcel->customer_phone_number = ltrim($request->customer_phone_number, implode('', $unsafeChars));
            $parcel->customer_address = ltrim($request->customer_address, implode('', $unsafeChars));
            $parcel->note = ltrim($request->note, implode('', $unsafeChars));

            // Charge
            $parcel->packaging = $packaging;
            $parcel->packaging_charge = $packaging_charge;
            $parcel->fragile = $fragile;
            $parcel->fragile_charge = $fragile_charge;
            $parcel->open_box = $request->open_box ?? 0;
            $parcel->home_delivery = $request->home_delivery ?? 0;

            $parcel->weight = $request->weight;
            $parcel->charge = $charge;
            $parcel->cod_charge = $cod_charge;
            $parcel->vat = $vat;
            $parcel->total_delivery_charge = floor($total_delivery_charge);
            $parcel->adjustment = $adjustment;
            $parcel->payable = ceil($payable);
            $parcel->location = $location;
            // End charge

            // pickup shop details
            $parcel->pickup_shop_phone_number = $request->shop_phone_number;
            $parcel->pickup_address = $request->shop_address;

            if ($request->has('shop') && $request->shop != '') {
                $shop = $merchant->shops->where('id', $request->shop)->first();
                if ($shop->pickup_branch_id) {
                    $parcel->pickup_branch_id = $shop->pickup_branch_id;
                } else {
                    $defaultShop = $merchant->shops->where('default', true)->first();
                    $parcel->pickup_branch_id = $defaultShop->pickup_branch_id;
                }
            }

            $parcel->shop_id = $request->shop != '' ? $request->shop : ($merchant->shops->where('default', true)->first() ? $merchant->shops->where('default', true)->first()->id : null);

            $destBranchId = $request->destination_branch_id ?? $request->transfer_branch_select_id;
            if ($request->has('transfer_to_branch') && !empty($destBranchId)) {
                $parcel->destination_branch_id = $destBranchId;
            } else {
                $parcel->destination_branch_id = null;
            }

            if ($parcel->parcel_type != $parcelType):
                if ($parcelType == 'frozen') {
                    $pickup_date = date('Y-m-d');
                    $pickup_time = date('h:i:s');
                    $delivery_date = date("Y-m-d", strtotime('+2 hours', strtotime($pickup_date)));
                    $delivery_time = date("h:i:s", strtotime('+2 hours', strtotime($pickup_time)));
                } elseif ($parcelType == 'same_day') {
                    if (date('H') > settingHelper('pickup_accept_start') && date('H') <= settingHelper('pickup_accept_end')) {
                        $pickup_date = date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d'))));
                        $delivery_date = date("Y-m-d", strtotime('+1 days', strtotime(date('Y-m-d'))));
                    } else {
                        $pickup_date = date('Y-m-d');
                        $delivery_date = date("Y-m-d");
                    }
                } else {
                    if (date('H') > settingHelper('pickup_accept_start') && date('H') <= settingHelper('pickup_accept_end')) {
                        $pickup_date = date('Y-m-d');
                        $delivery_date = date("Y-m-d", strtotime('+2 days', strtotime(date('Y-m-d'))));
                    } else {
                        $pickup_date = date('Y-m-d');
                        $delivery_date = date("Y-m-d", strtotime('+1 days', strtotime(date('Y-m-d'))));
                    }
                }

                $parcel->pickup_date = $pickup_date;
                if (isset($pickup_time)):
                    $parcel->pickup_time = $pickup_time ?? '';
                endif;
                $parcel->delivery_date = $delivery_date;
                if (isset($delivery_time)):
                    $parcel->delivery_time = $delivery_time ?? '';
                endif;
            endif;

            $parcel->user_id = $request->created_by != "" ? $request->created_by : Sentinel::getUser()->id;
            $parcel->parcel_type = $parcelType;
            $districtId = $request->district_id ?? $request->city_id ?? null;
            if ($districtId) {
                $parcel->district_id = $districtId;
            }
            if ($request->thana_id) {
                $parcel->thana_id = $request->thana_id;
            }
            $parcel->save();

            $this->parcelEvent($parcel->id, 'parcel_update_event');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function parcelDelete($request)
    {
        DB::beginTransaction();
        try {

            $parcel = Parcel::find($request->id);
            $parcel->status_before_cancel = $parcel->status;
            $parcel->status = 'deleted';
            $parcel->save();

            $this->parcelEvent($parcel->id, 'parcel_delete_event', '', '', '', $request->cancel_note);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function imageUpload($image, $type, $delivery_man_id)
    {
        $delivery = DeliveryMan::find($delivery_man_id);
        if ($delivery->driving_license != "" && file_exists($delivery->driving_license)):
            unlink($delivery->driving_license);
        endif;

        $requestImage = $image;
        $fileType = $requestImage->getClientOriginalExtension();
        $originalImage = date('YmdHis') . '-' . $type . rand(1, 50) . '.' . $fileType;
        $directory = 'admin/' . $type . '/';

        if (!is_dir($directory)) {
            mkdir($directory);
        }
        $originalImageUrl = $directory . $originalImage;
        Image::make($requestImage)->save($originalImageUrl, 80);
        return $originalImageUrl;
    }

    public function statusChange($request)
    {
        $user = User::find($request['id']);
        $user->status = $request['status'];
        $result = $user->save();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function assignPickupMan($request)
    {
        DB::beginTransaction();
        try {
            $parcel = Parcel::findOrFail($request->id);
            $parcel->status = 'pickup-assigned';
            $parcel->pickup_man_id = $request->pickup_man;
            $parcel->pickup_fee = DeliveryMan::find($request->pickup_man)->pick_up_fee;
            $parcel->save();

            if ($request->notify_pickup_man == 'notify'):
                $sms_body = $parcel->pickupMan->user->first_name . ', a pickup has been assigned to you. Address: ' . $parcel->pickup_address . ', Phone number: ' . $parcel->pickup_shop_phone_number . ', Pickup date: ' . $parcel->pickup_date;
                $this->test($sms_body, $parcel->pickupMan->phone_number, 'notify_pickup_man', setting('active_sms_provider'));


                $details = 'A pickup has assigned to you';
                $users = User::where('user_type', 'delivery')
                    ->whereHas('deliveryMan', function ($query) use ($request) {
                        $query->where('id', $request->pickup_man);
                    })
                    ->get();

                $permissions = ['notify_pickup_man'];
                $title = 'A pickup has assigned to you';

                $this->sendNotification($title, $users, $details, $permissions, 'success', url('api/v10/parcel-details/' . $parcel->id), '');

            endif;

            $this->parcelEvent($parcel->id, 'assign_pickup_man_event', '', $request->pickup_man, '', $request->note);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function assignDeliveryMan($request, $id = null, $type = null)
    {
        DB::beginTransaction();

        try {
            $parcel = Parcel::find($id);
            $parcel->status = 'delivery-assigned';
            $parcel->delivery_man_id = $request->delivery_man;
            $parcel->third_party_id = $request->third_party != '' ? $request->third_party : null;
            $parcel->delivery_fee = DeliveryMan::find($request->delivery_man)->delivery_fee;
            $parcel->save();

            $this->parcelEvent($parcel->id, 'assign_delivery_man_event', $request->delivery_man, '', '', $request->note);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollback();
            return false;
        }
    }
    // public function assignDeliveryMan($request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $parcel = Parcel::find($request->id);
    //         $parcel->status = 'delivery-assigned';
    //         $parcel->delivery_man_id = $request->delivery_man;
    //         $parcel->third_party_id = $request->third_party != '' ? $request->third_party : null;
    //         $parcel->delivery_fee = DeliveryMan::find($request->delivery_man)->delivery_fee;
    //         $parcel->save();

    //         $this->parcelEvent($parcel->id, 'assign_delivery_man_event', $request->delivery_man, '', '', $request->note);
    //         DB::commit();
    //         return true;

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return false;
    //     }
    // }

    public function parcelStatusUpdate($id, $status, $note, $branch = null, $delivery_man = null)
    {
        DB::beginTransaction();
        try {

            $parcel = Parcel::find($id);
            if ($status == 'cancel') {
                $parcel->status_before_cancel = $parcel->status;
            }
            if ($status == 'received') {
                $parcel->branch_id = $branch;
            }
            if ($status == 'transferred-to-branch') {
                $from_branch_id = $parcel->branch_id;
                $parcel->transfer_to_branch_id = $branch;
                $parcel->transfer_delivery_man_id = $delivery_man;

                if ($parcel) {
                    PercelMovement::create([
                        'parcel_id'        => $parcel->id,
                        'tracking_number'  => $parcel->parcel_no,
                        'from_branch_id'   => $from_branch_id,
                        'to_branch_id'     => $branch,
                        'sent_by'          => Sentinel::getUser()->id ?? null,
                        'delivery_man_id'  => $delivery_man,
                        'sent_at'          => now(),
                        'status'           => 'in_transit',
                        'note'             => $note ?? 'Transferred to branch/hub',
                    ]);
                }
            }
            if ($status == 'transferred-received-by-branch') {
                $parcel->branch_id = $branch;
                $parcel->transfer_to_branch_id = null;

                $activeMovement = PercelMovement::where('parcel_id', $parcel->id)
                    ->where('status', 'in_transit')
                    ->latest()
                    ->first();
                if ($activeMovement) {
                    $activeMovement->update([
                        'received_by' => Sentinel::getUser()->id ?? null,
                        'received_at' => now(),
                        'status'      => 'received',
                    ]);
                }
            }
            if ($status == 're-request') {
                $parcel->status = $parcel->status_before_cancel;
            } else {
                $parcel->status = $status;
            }
            $parcel->date = date('Y-m-d');

            $this->accounts->incomeExpenseManage($id, $status);

            if ($status == 'cancel') {
                $this->parcelEvent($parcel->id, 'parcel_cancel_event', '', '', '', $note);
            } elseif ($status == 'received-by-pickup-man') {
                $this->parcelEvent($parcel->id, 'parcel_received_by_pickup_man_event', '', '', '', $note);
            } elseif ($status == 'received') {
                $this->parcelEvent($parcel->id, 'parcel_received_event', '', '', '', $note, '', $branch);
            } elseif ($status == 'transferred-to-branch') {
                $this->parcelEvent($parcel->id, 'parcel_transferred_to_branch_assigned_event', '', '', '', $note, '', $branch, $delivery_man);
            } elseif ($status == 'transferred-received-by-branch') {
                $this->parcelEvent($parcel->id, 'parcel_transferred_to_branch_event', '', '', '', $note, '', $branch);
            } elseif ($status == 'returned-to-warehouse') {
                $this->parcelEvent($parcel->id, 'parcel_return_to_warehouse_event', '', '', '', $note);
            } elseif ($status == 'delivered') {
                $parcel->otp = rand(1000, 9999);
                $parcel->otp_expired_at = Carbon::now()->addMinutes(5);
                $parcel->otp_attempts = 0;
                $this->parcelEvent($parcel->id, 'parcel_delivered_event', '', '', '', $note);
                $parcel->delivered_date = date('Y-m-d');

                // Log OTP Generation by Admin
                try {
                    $currentUser = Sentinel::getUser();
                    PercelOtpLog::create([
                        'parcel_id'       => $parcel->id,
                        'user_id'         => $currentUser->id ?? null,
                        'action'          => 'generated',
                        'otp_code'        => $parcel->otp,
                        'attempt_number'  => 0,
                        'source'          => 'admin_panel',
                        'status_message'  => 'Delivery OTP generated by Admin/Staff',
                    ]);
                } catch (\Exception $logEx) {}

                //sending delivery confirm otp to customer
                $sms_template = CustomerParcelSmsTemplates::where('subject', 'delivery_confirm_otp')->first();

                if ($sms_template->sms_to_customer):
                    $sms_body = str_replace('{merchant_name}', $parcel->merchant->company, $sms_template->content);
                    $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
                    $sms_body = str_replace('{otp}', $parcel->otp, $sms_body);
                    $sms_body = str_replace('{our_company_name}', setting('company_name'), $sms_body);
                    $this->test($sms_body, $parcel->customer_phone_number, 'delivery_confirm_otp', $sms_template->masking, setting('active_sms_provider'));
                endif;
            } elseif ($status == 'returned-to-merchant') {
                $parcel->returned_date = date('Y-m-d');

                $this->parcelEvent($parcel->id, 'parcel_returned_to_merchant_event', '', '', '', $note);
            } elseif ($status == 're-request') {
                $this->parcelEvent($parcel->id, 'parcel_re_request_event', '', '', '', $note);
            }

            $parcel->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    public function parcelCancel($request)
    {
        DB::beginTransaction();
        try {

            $parcel = Parcel::find($request->id);
            $parcel->status_before_cancel = $parcel->status;
            $parcel->status = 'cancel';
            $parcel->save();

            $note = __($request->predefined_reason) . ' ' . $request->cancel_note;

            $this->parcelEvent($parcel->id, 'parcel_cancel_event', '', '', '', $note);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function deliveryReverse($request)
    {
        DB::beginTransaction();
        try {

            $this->accounts->incomeExpenseManageReverse($request->id, $request->status);

            $parcel = Parcel::find($request->id);
            $previous_status = $parcel->status;
            //end
            $parcel->status = $request->status;
            $parcel->date = date('Y-m-d');

            $previously_partially_delivered = false;

            if (
                ($previous_status == 'partially-delivered' || $previous_status == 'returned-to-warehouse' || $previous_status == 'return-assigned-to-merchant'
                    || $previous_status == 'returned-to-merchant') && $parcel->is_partially_delivered && ($request->status == 'pending' || $request->status == 'pickup-assigned'
                    || $request->status == 'received-by-pickup-man' || $request->status == 'received' || $request->status == 'transferred-received-by-branch' || $request->status == 'delivery-assigned')
            ):

                if (number_format($parcel->price_before_delivery, 2, '.', '') != number_format($parcel->price, 2, '.', '')):
                    $location = $parcel->location;

                    // Start Charge calculate
                    $merchant = $this->merchants->get($parcel->merchant_id);
                    $charge = data_get($merchant->charges, $parcel->weight . '.' . $parcel->parcel_type);
                    $cod_charge = data_get($merchant->cod_charges, $location);
                    $vat = $merchant->vat ?? 0.00;
                    $total_delivery_charge = $charge + $parcel->packaging_charge + $parcel->fragile_charge + $parcel->price_before_delivery / 100 * $cod_charge;
                    $total_delivery_charge += $total_delivery_charge / 100 * $vat;

                    $payable = $parcel->price_before_delivery - number_format($total_delivery_charge, 2);
                    // End charge calculate

                    $parcel->price = $parcel->price_before_delivery;

                    // Charge
                    $parcel->charge = $charge;
                    $parcel->cod_charge = $cod_charge;
                    $parcel->vat = $vat;
                    $parcel->total_delivery_charge = floor($total_delivery_charge);
                    $parcel->payable = ceil($payable);
                // End charge
                endif;
                $previously_partially_delivered = true;
                $parcel->is_partially_delivered = false;
            endif;

            $parcel->save();

            //if previous delivery reverse, reverse that also
            $previous_reverse = ParcelEvent::where('parcel_id', $request->id)->where('title', 'delivery_reverse_event')->latest()->first();

            if (!blank($previous_reverse)):
                $previous_reverse->reverse_status = 'reversed';
                $previous_reverse->save();
            endif;

            if ($previous_status == 'pickup-assigned'):
                foreach ($parcel->events as $event):
                    $event->reverse_status = 'reversed';
                    $event->save();
                endforeach;

                $title = 'parcel_create_event';

            elseif ($previous_status == 'deleted'):

                foreach ($parcel->events as $event):
                    $event->reverse_status = 'reversed';
                    $event->save();
                endforeach;

                $title = 'parcel_pending_event';


            elseif ($previous_status == 'received-by-pickup-man' || $previous_status == 're-schedule-pickup'):

                if ($previous_status == 'received-by-pickup-man'):
                    $received = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_received_by_pickup_man_event')->latest()->first();
                    $received->reverse_status = 'reversed';
                    $received->save();
                endif;

                $title = 'parcel_received_by_pickup_man_event';

                if ($request->status == 'pickup-assigned' || $request->status == 'pending'):
                    $title = $this->requestPickupPending($request);
                endif;

            elseif ($previous_status == 'received'):
                $title = $this->uptoReceived($request);
            elseif ($previous_status == 'transferred-to-branch'):
                $title = $this->uptoTransfer($request);
            elseif ($previous_status == 'transferred-received-by-branch'):
                $title = $this->uptoTransfer($request);
            elseif ($previous_status == 'delivery-assigned' || $previous_status == 're-schedule-delivery'):
                $reschedule_events = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_re_schedule_delivery_event')->latest()->get();
                foreach ($reschedule_events as $event):
                    $event->reverse_status = 'reversed';
                    $event->save();
                endforeach;
                $title = 'assign_delivery_man_event';
                $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'assign_delivery_man_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();

                if (
                    $request->status == 'transferred-received-by-branch' || $request->status == 'transferred-to-branch' ||
                    $request->status == 'received' || $request->status == 'received-by-pickup-man' ||
                    $request->status == 'pickup-assigned' || $request->status == 'pending'
                ):

                    $parcel->third_party_id = null;
                    $parcel->save();

                    $title = $this->uptoTransfer($request);
                endif;

            elseif ($previous_status == 'partially-delivered'):
                $title = $this->uptoPartialDelivery($request);
            elseif ($previous_status == 'returned-to-warehouse' && $previously_partially_delivered):
                //reversing previous 'parcel_return_to_warehouse_event'
                $received = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_return_to_warehouse_event')->latest()->first();
                $received->reverse_status = 'reversed';
                $received->save();

                $title = $this->uptoPartialDelivery($request);
            elseif ($previous_status == 'return-assigned-to-merchant' && $previously_partially_delivered):
                $title = $this->returnAssignToMerchantPartialEvent($request);

            elseif ($previous_status == 'returned-to-merchant' && $previously_partially_delivered):
                $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_returned_to_merchant_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();

                if (
                    $request->status == 'pending' || $request->status == 'pickup-assigned' || $request->status == 'received-by-pickup-man'
                    || $request->status == 'received' || $request->status == 'transferred-received-by-branch' || $request->status == 'delivery-assigned'
                    || $request->status == 'partially-delivered' || $request->status == 'partially-delivered' || $request->status == 'return-assigned-to-merchant'
                ):

                    $title = $this->returnAssignToMerchantPartialEvent($request);
                endif;

            //end

            elseif ($previous_status == 'delivered' || $previous_status == 'returned-to-warehouse'):
                if ($previous_status == 'delivered'):
                    //reversing previous upto 'delivery-assigned' and insert 'assign_delivery_man_event'
                    $received = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_delivered_event')->latest()->first();
                    $received->reverse_status = 'reversed';
                    $received->save();
                else:
                    //reversing previous 'parcel_return_to_warehouse_event'
                    $received = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_return_to_warehouse_event')->latest()->first();
                    $received->reverse_status = 'reversed';
                    $received->save();
                //end reverse 'parcel_return_to_warehouse_event'
                endif;

                if ($request->status != 'returned-to-warehouse' && $request->status != 'delivered'):
                    $reschedule_events = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_re_schedule_delivery_event')->latest()->get();
                    foreach ($reschedule_events as $event):
                        $event->reverse_status = 'reversed';
                        $event->save();
                    endforeach;

                    $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'assign_delivery_man_event')->latest()->first();
                    $parcel_event->reverse_status = 'reversed';
                    $parcel_event->save();

                    $title = 'assign_delivery_man_event';
                    // upto delivery-assigned reversed and insert 'assign_delivery_man_event' end

                    if (
                        $request->status == 'transferred-received-by-branch' || $request->status == 'transferred-to-branch' ||
                        $request->status == 'received' || $request->status == 'received-by-pickup-man' ||
                        $request->status == 'pickup-assigned' || $request->status == 'pending'
                    ):

                        $title = $this->uptoTransfer($request);
                    endif;

                elseif ($request->status == 'returned-to-warehouse' || $request->status == 'delivered'):
                    if ($request->status == 'returned-to-warehouse'):
                        $title = 'parcel_return_to_warehouse_event';
                    else:
                        $title = 'parcel_delivered_event';
                    endif;
                endif;
            elseif ($previous_status == 'return-assigned-to-merchant'):
                $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_return_assign_to_merchant_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();

                //reverse and re-insert
                $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_return_to_warehouse_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();

                $title = 'parcel_return_to_warehouse_event';
                //end

                if ($request->status == 'delivery-assigned' || $request->status == 'transferred-received-by-branch' || $request->status == 'transferred-to-branch' || $request->status == 'received' || $request->status == 'received-by-pickup-man' || $request->status == 'pickup-assigned' || $request->status == 'pending'):
                    $title = $this->uptoDeliveryAssigned($request);
                endif;
            elseif ($previous_status == 'returned-to-merchant'):
                $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_returned_to_merchant_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();

                //reverse and re-insert
                $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_return_assign_to_merchant_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();

                $title = 'parcel_return_assign_to_merchant_event';
                //end

                if ($request->status == 'returned-to-warehouse' || $request->status == 'delivery-assigned' || $request->status == 'transferred-received-by-branch' || $request->status == 'transferred-to-branch' || $request->status == 'received' || $request->status == 'received-by-pickup-man' || $request->status == 'pickup-assigned' || $request->status == 'pending'):
                    $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_return_to_warehouse_event')->latest()->first();
                    $parcel_event->reverse_status = 'reversed';
                    $parcel_event->save();

                    $title = 'parcel_return_to_warehouse_event';

                    if ($request->status == 'delivery-assigned' || $request->status == 'transferred-received-by-branch' || $request->status == 'transferred-to-branch' || $request->status == 'received' || $request->status == 'received-by-pickup-man' || $request->status == 'pickup-assigned' || $request->status == 'pending'):
                        $title = $this->uptoDeliveryAssigned($request);
                    endif;
                endif;
            endif;

            $this->parcelEvent($parcel->id, 'delivery_reverse_event', '', '', '', $request->note);
            $this->parcelEvent($parcel->id, $title, @$parcel->delivery_man_id, @$parcel->pickup_man_id, @$parcel->return_delivery_man_id, '', 'reverse');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function requestPending($id)
    {
        $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_create_event')->latest()->first();
        $parcel_event->reverse_status = 'reversed';
        $parcel_event->save();
        return 'parcel_create_event';
    }

    public function requestPickupPending($request)
    {
        $reschedule_events = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_re_schedule_pickup_event')->latest()->get();
        foreach ($reschedule_events as $event):
            $event->reverse_status = 'reversed';
            $event->save();
        endforeach;

        $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'assign_pickup_man_event')->latest()->first();
        $parcel_event->reverse_status = 'reversed';
        $parcel_event->save();

        $title = 'assign_pickup_man_event';

        if ($request->status == 'pending'):
            $title = $this->requestPending($request->id);
        endif;

        return $title;
    }

    public function requestPickupManReceivedPickupPending($request)
    {
        $event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_received_by_pickup_man_event')->latest()->first();
        if (!blank($event)):
            $event->reverse_status = 'reversed';
            $event->save();
        endif;

        $title = 'parcel_received_by_pickup_man_event';

        if ($request->status == 'pickup-assigned' || $request->status == 'pending'):
            $title = $this->requestPickupPending($request);
        endif;

        return $title;
    }

    public function uptoReceived($request)
    {
        //reverse received and insert received again
        $title = 'parcel_received_event';

        $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_received_event')->latest()->first();
        if (!blank($parcel_event)) {
            $parcel_event->reverse_status = 'reversed';
            $parcel_event->save();
        }

        //end received

        if ($request->status == 'received-by-pickup-man' || $request->status == 'pickup-assigned' || $request->status == 'pending'):
            $title = $this->requestPickupManReceivedPickupPending($request);
            $parcel = $this->get($request->id);
            $parcel->branch_id = null;
            $parcel->save();
        endif;

        return $title;
    }

    public function uptoTransfer($request)
    {
        $events = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_transferred_to_branch_event')->latest()->get();

        foreach ($events as $event):
            $event->reverse_status = 'reversed';
            $event->save();
        endforeach;
        $title = 'parcel_transferred_to_branch_event';
        if (
            $request->status == 'transferred-to-branch' ||
            $request->status == 'received' || $request->status == 'received-by-pickup-man' ||
            $request->status == 'pickup-assigned' || $request->status == 'pending'
        ):

            $events = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_transferred_to_branch_assigned_event')->latest()->get();
            foreach ($events as $event):
                $event->reverse_status = 'reversed';
                $event->save();
            endforeach;

            $title = 'parcel_transferred_to_branch_assigned_event';
            if (
                $request->status == 'received' || $request->status == 'received-by-pickup-man' ||
                $request->status == 'pickup-assigned' || $request->status == 'pending'
            ):

                $parcel = $this->get($request->id);
                $parcel->transfer_to_branch_id = null;
                $parcel->transfer_delivery_man_id = null;
                $parcel->save();

                $title = $this->uptoReceived($request);

            endif;

        endif;

        return $title;
    }

    public function uptoDeliveryAssigned($request)
    {
        $reschedule_events = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_re_schedule_delivery_event')->latest()->get();
        foreach ($reschedule_events as $event):
            $event->reverse_status = 'reversed';
            $event->save();
        endforeach;

        $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'assign_delivery_man_event')->latest()->first();
        if (!blank($parcel_event)) {
            $parcel_event->reverse_status = 'reversed';
            $parcel_event->save();
        }

        $title = 'assign_delivery_man_event';

        if (
            $request->status == 'transferred-received-by-branch' || $request->status == 'transferred-to-branch' ||
            $request->status == 'received' || $request->status == 'received-by-pickup-man' ||
            $request->status == 'pickup-assigned' || $request->status == 'pending'
        ):
            $title = $this->uptoTransfer($request);
        endif;

        return $title;
    }

    public function uptoPartialDelivery($request)
    {
        $received = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_partial_delivered_event')->latest()->first();
        $received->reverse_status = 'reversed';
        $received->save();

        $title = 'parcel_partial_delivered_event';

        if (
            $request->status == 'pending' || $request->status == 'pickup-assigned'
            || $request->status == 'received-by-pickup-man' || $request->status == 'received' || $request->status == 'transferred-received-by-branch' || $request->status == 'delivery-assigned'
        ):
            $reschedule_events = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_re_schedule_delivery_event')->latest()->get();
            foreach ($reschedule_events as $event):
                $event->reverse_status = 'reversed';
                $event->save();
            endforeach;

            $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'assign_delivery_man_event')->latest()->first();
            $parcel_event->reverse_status = 'reversed';
            $parcel_event->save();

            $title = 'assign_delivery_man_event';
            // upto delivery-assigned reversed and insert 'assign_delivery_man_event' end

            if (
                $request->status == 'transferred-received-by-branch' || $request->status == 'transferred-to-branch' ||
                $request->status == 'received' || $request->status == 'received-by-pickup-man' ||
                $request->status == 'pickup-assigned' || $request->status == 'pending'
            ):

                $title = $this->uptoTransfer($request);
            endif;
        endif;

        return $title;
    }

    public function returnAssignToMerchantPartialEvent($request)
    {
        $parcel_event = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_return_assign_to_merchant_event')->latest()->first();
        $parcel_event->reverse_status = 'reversed';
        $parcel_event->save();

        $title = 'parcel_return_assign_to_merchant_event';

        if (
            $request->status == 'pending' || $request->status == 'pickup-assigned' || $request->status == 'received-by-pickup-man'
            || $request->status == 'received' || $request->status == 'transferred-received-by-branch' || $request->status == 'delivery-assigned'
            || $request->status == 'partially-delivered' || $request->status == 'returned-to-warehouse'
        ):

            //reversing previous 'parcel_return_to_warehouse_event'
            $received = ParcelEvent::where('parcel_id', $request->id)->where('title', 'parcel_return_to_warehouse_event')->latest()->first();
            $received->reverse_status = 'reversed';
            $received->save();

            $title = 'parcel_return_to_warehouse_event';

            if (
                $request->status == 'pending' || $request->status == 'pickup-assigned'
                || $request->status == 'received-by-pickup-man' || $request->status == 'received' || $request->status == 'transferred-received-by-branch' || $request->status == 'delivery-assigned'
                || $request->status == 'partially-delivered'
            ):
                $title = $this->uptoPartialDelivery($request);
            endif;
        endif;

        return $title;
    }

    public function reSchedulePickupMan($request)
    {
        DB::beginTransaction();
        try {
            $parcel = Parcel::find($request->id);
            $parcel->status = 're-schedule-pickup';
            $parcel->pickup_date = date('Y-m-d', strtotime($request->date));
            $parcel->pickup_time = date('h:i:s', strtotime($request->time));
            $parcel->pickup_man_id = $request->pickup_man;
            $parcel->pickup_fee = DeliveryMan::find($request->pickup_man)->pick_up_fee;
            $parcel->save();

            if ($request->notify_pickup_man == 'notify'):
                $sms_body = $parcel->pickupMan->user->first_name . ', a pickup has been re-scheduled and assigned to you. Address: ' . $parcel->pickup_address . ', Phone number: ' . $parcel->pickup_shop_phone_number . ', Pickup date: ' . $parcel->pickup_date;
                $this->test($sms_body, $parcel->pickupMan->phone_number, 'notify_pickup_man', setting('active_sms_provider'));
            endif;

            $note = __($request->predefined_reason) . ' ' . $request->note;

            $this->parcelEvent($parcel->id, 'parcel_re_schedule_pickup_event', '', $request->pickup_man, '', $note);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    public function reScheduleDeliveryMan($request)
    {
        DB::beginTransaction();
        try {
            $parcel = Parcel::find($request->id);
            $parcel->status = 're-schedule-delivery';
            $parcel->delivery_date = date('Y-m-d', strtotime($request->date));
            $parcel->delivery_time = date('h:i:s', strtotime($request->time));
            $parcel->delivery_man_id = $request->delivery_man;
            $parcel->delivery_fee = DeliveryMan::find($request->delivery_man)->delivery_fee;
            $parcel->third_party_id = $request->third_party != '' ? $request->third_party : null;
            $parcel->save();

            $note = __($request->predefined_reason) . ' ' . $request->note;

            $this->parcelEvent($parcel->id, 'parcel_re_schedule_delivery_event', $request->delivery_man, '', '', $note);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    public function returnAssignToMerchant($request, $id)
    {
        DB::beginTransaction();
        try {
            $parcel = Parcel::find($id);
            $parcel->status = 'return-assigned-to-merchant';
            $parcel->return_delivery_man_id = $request->delivery_man;
            $parcel->return_fee = DeliveryMan::find($request->delivery_man)->return_fee;
            $parcel->save();

            $this->parcelEvent($parcel->id, 'parcel_return_assign_to_merchant_event', '', '', $request->delivery_man, $request->note);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function reSchedulePickup($request)
    {
        $delivery_men = DeliveryMan::with([
            'user' => function ($query) {
                $query->where('status', 1);
            }
        ])->get();

        $parcel = $this->get($request->id);
        $options = "<option value=''>" . __('select_pickup_man') . "</option>";
        foreach ($delivery_men as $delivery_man):
            if ($parcel->pickup_man_id == $delivery_man->id) {
                $options .= "<option value='$delivery_man->id' selected>" . @$delivery_man->user->first_name . ' ' . @$delivery_man->user->last_name . "</option>";
            }
        endforeach;

        $data[1] = $options;
        $data[2] = date('m/d/Y', strtotime($parcel->pickup_date));
        $data[3] = date('h:i a', strtotime($parcel->pickup_time));
        $data[4] = $parcel->parcel_type;
        $data[5] = $parcel->events->whereIn('title', ['assign_pickup_man_event', 'parcel_re_schedule_pickup_event'])->first()->cancel_note;
        return $data;
    }
    public function reScheduleDelivery($request)
    {
        $delivery_men = DeliveryMan::with([
            'user' => function ($query) {
                $query->where('status', 1);
            }
        ])->get();
        $parcel = $this->get($request->id);

        foreach ($delivery_men as $delivery_man):
            if ($parcel->delivery_man_id == $delivery_man->id) {
                $options .= "<option value='$delivery_man->id' selected>" . $delivery_man->user->first_name . ' ' . $delivery_man->user->last_name . "</option>";
            }
        endforeach;

        $data[1] = $options;
        $data[2] = date('m/d/Y', strtotime($parcel->delivery_date));
        $data[3] = date('h:i a', strtotime($parcel->delivery_time));
        $data[4] = $parcel->parcel_type;
        $data[5] = $parcel->events->whereIn('title', ['assign_delivery_man_event', 'parcel_re_schedule_delivery_event'])->first()->cancel_note;

        $third_party_options = "<option value=''>" . __('select_delivery_man') . "</option>";

        if ($parcel->third_party_id != null):
            $third_party = ThirdParty::find($parcel->third_party_id);
            $third_party_options .= "<option value='$third_party->id' selected>" . $third_party->name . ' (' . $third_party->address . ")</option>";
        endif;

        $data[6] = $parcel->location;
        $data[7] = $third_party_options;
        return $data;
    }

    public function parcelEvent($parcel_id, $title, $delivery_man = '', $pickup_man = '', $return_delivery_man = '', $cancel_note = '', $status = '', $branch = null, $transfer_delivery_man = null, $created_at = '')
    {

        $parcel = $this->get($parcel_id);
        $parcel_event = new ParcelEvent();

      
        $actionMap = [
            'parcel_create_event'                        => 'status_change',
            'parcel_update_event'                        => 'status_change',
            'parcel_delete_event'                        => 'status_change',
            'parcel_cancel_event'                        => 'status_change',
            'assign_pickup_man_event'                    => 'rider_assign',
            'assign_delivery_man_event'                  => 'rider_assign',
            'parcel_received_by_pickup_man_event'        => 'status_change',
            'parcel_received_event'                      => 'status_change',
            'parcel_transferred_to_branch_event'         => 'status_change',
            'parcel_transferred_to_branch_assigned_event'=> 'rider_assign',
            'parcel_return_to_warehouse_event'           => 'return',
            'parcel_delivered_event'                     => 'status_change',
            'parcel_delivered_and_verified_event'        => 'status_change',
            'parcel_returned_to_merchant_event'          => 'return',
            'parcel_return_assign_to_merchant_event'     => 'return',
            'parcel_re_schedule_pickup_event'            => 'status_change',
            'parcel_re_schedule_delivery_event'          => 'status_change',
            'parcel_re_request_event'                    => 'status_change',
            'parcel_partial_delivered_event'             => 'status_change',
            'delivery_reverse_event'                     => 'status_change',
            'cancel_reverse_event'                       => 'status_change',
            'delete_reverse_event'                       => 'status_change',
        ];
        $auditAction = $actionMap[$title] ?? 'status_change';

        $oldStatus = ParcelEvent::$lastOldStatus[$parcel_id] ?? ($parcel->status_before_cancel ?? $parcel->status);

        if ($title == 'delivery_reverse_event' || $title == 'cancel_reverse_event' || $status == 'reverse'):

            $parcel_event->parcel_id   = $parcel_id;
            $parcel_event->delivery_man_id = $parcel->delivery_man_id;
            $parcel_event->pickup_man_id   = $parcel->pickup_man_id;
            $parcel_event->user_id         = Sentinel::getUser()->id ?? $parcel->user_id;
            $parcel_event->title           = $title;
            $parcel_event->cancel_note     = $cancel_note;
            $parcel_event->branch_id       = $branch ?? $parcel->branch_id;
            $parcel_event->third_party_id  = $parcel->third_party_id;
            $parcel_event->transfer_delivery_man_id = $transfer_delivery_man ?? $parcel->transfer_delivery_man_id;
            // Audit fields
            $parcel_event->old_status  = $oldStatus;
            $parcel_event->new_status  = $parcel->status ?? null;
            $parcel_event->ip_address  = request()->ip();
            $parcel_event->action      = $auditAction;
            $parcel_event->save();

            return true;
        else:
            $parcel_event->parcel_id       = $parcel_id;
            $parcel_event->delivery_man_id = $parcel->delivery_man_id;
            $parcel_event->pickup_man_id   = $parcel->pickup_man_id;
            if ($created_at != ''):
                $parcel_event->user_id = 2260;
            else:
                $parcel_event->user_id = Sentinel::getUser()->id ?? $parcel->user_id;
            endif;
            $parcel_event->title           = $title;
            $parcel_event->cancel_note     = $cancel_note;
            $parcel_event->branch_id       = $branch ?? $parcel->branch_id;
            $parcel_event->third_party_id  = $parcel->third_party_id;
            $parcel_event->transfer_delivery_man_id = $transfer_delivery_man ?? $parcel->transfer_delivery_man_id;
            // Audit fields
            $parcel_event->old_status  = $oldStatus;
            $parcel_event->new_status  = $parcel->status ?? null;
            $parcel_event->ip_address  = request()->ip();
            $parcel_event->action      = $auditAction;

            if ($created_at != ''):
                $parcel_event->created_at = $created_at;
            endif;
        endif;

        $delivery_person = DeliveryMan::where('id', $parcel->delivery_man_id)->first();
        $pickup_person = DeliveryMan::where('id', $parcel->pickup_man_id)->first();

        // merchant sms start
        $sms_template = SmsTemplate::where('subject', $title)->first();

        if (!blank($sms_template)):
            if ($sms_template->sms_to_merchant):
                $sms_body = str_replace('{merchant_name}', $parcel->merchant->company, $sms_template->content);
                $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
                $sms_body = str_replace('{pickup_date_time}', date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
                $sms_body = str_replace('{re_pickup_date_time}', date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
                $sms_body = str_replace('{delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
                $sms_body = str_replace('{re_delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
                if ($created_at != ''):
                    $sms_body = str_replace('{current_date_time}', date('M d, Y h:i a', strtotime($created_at)), $sms_body);
                else:
                    $sms_body = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);
                endif;
                $sms_body = str_replace('{return_date_time}', date('M d, Y h:i a'), $sms_body);
                $sms_body = str_replace('{our_company_name}', setting('company_name'), $sms_body);
                $sms_body = str_replace('{pickup_man_name}', @$pickup_person->user->first_name, $sms_body);
                $sms_body = str_replace('{pickup_man_phone}', @$pickup_person->phone_number, $sms_body);
                $sms_body = str_replace('{delivery_man_name}', @$delivery_person->user->first_name, $sms_body);
                $sms_body = str_replace('{delivery_man_phone}', @$delivery_person->phone_number, $sms_body);
                $sms_body = str_replace('{cancel_note}', @$parcel->cancelnote->cancel_note, $sms_body);
                $sms_body = str_replace('{price}', @$parcel->price, $sms_body);
                $sms_body = str_replace('{short_url}', @$parcel->short_url, $sms_body);
                //send sms
                $this->test($sms_body, $parcel->merchant->phone_number, $title, setting('active_sms_provider'), $sms_template->masking);
            endif;
        //merchant sms end
        endif;

        //customer sms start
        if ($this->checkLocation($parcel, $title)):
            $customer_sms_template = CustomerParcelSmsTemplates::where('subject', $title)->first();
            if (!blank($customer_sms_template)):
                if ($customer_sms_template->sms_to_customer):
                    $sms_body = str_replace('{merchant_name}', $parcel->merchant->company, $customer_sms_template->content);
                    $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
                    $sms_body = str_replace('{pickup_date_time}', date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
                    $sms_body = str_replace('{re_pickup_date_time}', date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
                    $sms_body = str_replace('{delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
                    $sms_body = str_replace('{re_delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
                    $sms_body = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);
                    $sms_body = str_replace('{return_date_time}', date('M d, Y h:i a'), $sms_body);
                    $sms_body = str_replace('{our_company_name}', setting('company_name'), $sms_body);
                    $sms_body = str_replace('{pickup_man_name}', @$pickup_person->user->first_name, $sms_body);
                    $sms_body = str_replace('{pickup_man_phone}', @$pickup_person->phone_number, $sms_body);
                    $sms_body = str_replace('{delivery_man_name}', @$delivery_person->user->first_name, $sms_body);
                    $sms_body = str_replace('{delivery_man_phone}', @$delivery_person->phone_number, $sms_body);
                    $sms_body = str_replace('{cancel_note}', @$parcel->cancelnote->cancel_note, $sms_body);
                    $sms_body = str_replace('{price}', @$parcel->price, $sms_body);
                    $sms_body = str_replace('{short_url}', @$parcel->short_url, $sms_body);
                    //send sms
                    $this->test($sms_body, $parcel->customer_phone_number, $title, setting('active_sms_provider'), $customer_sms_template->masking);
                endif;
            //customer sms end
            endif;
        endif;

        if ($delivery_man != ""):
            $parcel_event->delivery_man_id = $delivery_man;
        endif;
        if ($pickup_man != ""):
            $parcel_event->pickup_man_id = $pickup_man;
        endif;
        if ($return_delivery_man != ""):
            $parcel_event->return_delivery_man_id = $return_delivery_man;
        endif;
        if ($transfer_delivery_man != ""):
            $parcel_event->transfer_delivery_man_id = $transfer_delivery_man;
        endif;
        $parcel_event->save();
        return $parcel_event;
    }

    public function checkLocation($parcel, $title)
    {
        if ($parcel->location == 'sub_urban_area' || $parcel->location == 'sub_city'):
            if ($title != 'assign_delivery_man_event'):
                return false;
            endif;
        endif;

        return true;
    }

    public function reverseUpdate($id, $status, $note = '')
    {
        DB::beginTransaction();
        try {

            $parcel = Parcel::find($id);
            $reverse_type = $parcel->status;
            $parcel->status = $status;
            $parcel->date = date('Y-m-d');
            $parcel->save();

            if ($reverse_type == 'cancel') {
                $previous_cancel = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_cancel_event')->latest()->first();
                if (!blank($previous_cancel)):
                    $previous_cancel->reverse_status = 'reversed';
                    $previous_cancel->save();
                endif;
                $cancel_reverse_event = ParcelEvent::where('parcel_id', $id)->where('title', 'cancel_reverse_event')->latest()->first();
                if (!blank($cancel_reverse_event)):
                    $cancel_reverse_event->reverse_status = 'reversed';
                    $cancel_reverse_event->save();
                endif;
            } else {

                $previous_cancel = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_delete_event')->latest()->first();
                if (!blank($previous_cancel)):
                    $previous_cancel->reverse_status = 'reversed';
                    $previous_cancel->save();
                endif;
                $cancel_reverse_event = ParcelEvent::where('parcel_id', $id)->where('title', 'delete_reverse_event')->latest()->first();
                if (!blank($cancel_reverse_event)):
                    $cancel_reverse_event->reverse_status = 'reversed';
                    $cancel_reverse_event->save();
                endif;
            }

            if ($status == 'pending'):
                $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_create_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();
                $title = 'parcel_create_event';
            elseif ($status == 'pickup-assigned'):
                $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'assign_pickup_man_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();
                $title = 'assign_pickup_man_event';
            elseif ($status == 're-schedule-pickup'):
                $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_re_schedule_pickup_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();
                $title = 'parcel_re_schedule_pickup_event';
            elseif ($status == 'received-by-pickup-man'):
                $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_received_by_pickup_man_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();
                $title = 'parcel_received_by_pickup_man_event';
            elseif ($status == 'received'):
                $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_received_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();
                $title = 'parcel_received_event';
            elseif ($status == 'transferred-to-branch'):
                $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_transferred_to_branch_assigned_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();
                $title = 'parcel_transferred_to_branch_assigned_event';
            elseif ($status == 'transferred-received-by-branch'):
                $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_transferred_to_branch_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();
                $title = 'parcel_transferred_to_branch_event';
            elseif ($status == 'delivery-assigned'):
                $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'assign_delivery_man_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();
                $title = 'assign_delivery_man_event';
            elseif ($status == 're-schedule-delivery'):
                $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_re_schedule_delivery_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();
                $title = 'parcel_re_schedule_delivery_event';
            elseif ($status == 'returned-to-warehouse'):
                $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_return_to_warehouse_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();
                $title = 'parcel_return_to_warehouse_event';
            elseif ($status == 'return-assigned-to-merchant'):
                $parcel_event = ParcelEvent::where('parcel_id', $id)->where('title', 'parcel_return_assign_to_merchant_event')->latest()->first();
                $parcel_event->reverse_status = 'reversed';
                $parcel_event->save();
                $title = 'parcel_return_assign_to_merchant_event';
            endif;

            if ($reverse_type == 'cancel') {
                $this->parcelEvent($parcel->id, 'cancel_reverse_event', '', '', '', $note);
            } else {
                $this->parcelEvent($parcel->id, 'delete_reverse_event', '', '', '', $note);
            }


            $this->parcelEvent($parcel->id, $title, @$parcel->delivery_man_id, @$parcel->pickup_man_id, @$parcel->return_delivery_man_id, '', 'reverse');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    //partial delivery
    public function partialDelivery($request)
    {
        DB::beginTransaction();
        try {
            $parcel = $this->get($request->id);
            $parcel->price_before_delivery = $parcel->price;

            if (number_format($parcel->price, 2, '.', '') != number_format($request->cod, 2, '.', '')):
                $location = $parcel->location;

                $merchant = $this->merchants->get($parcel->merchant_id);
                $charge = data_get($merchant->charges, $parcel->weight . '.' . $parcel->parcel_type);
                $cod_charge = data_get($merchant->cod_charges, $location);
                $vat = $merchant->vat ?? 0.00;
                $total_delivery_charge = $charge + $parcel->packaging_charge + $parcel->fragile_charge + $request->cod / 100 * $cod_charge;
                $total_delivery_charge += $total_delivery_charge / 100 * $vat;
                $payable = $request->cod - number_format($total_delivery_charge, 2);


                // Charge
                $parcel->charge = $charge;
                $parcel->cod_charge = $cod_charge;
                $parcel->vat = $vat;
                $parcel->total_delivery_charge = floor($total_delivery_charge);
                $parcel->payable = ceil($payable);
                // End charge

                $parcel->price = $request->cod;
            endif;
            $parcel->status = 'partially-delivered';
            $parcel->is_partially_delivered = true;

            $parcel->date = date('Y-m-d');
            $this->parcelEvent($parcel->id, 'parcel_partial_delivered_event', '', '', '', $request->note);
            $parcel->save();
            $this->accounts->incomeExpenseManage($parcel->id, $parcel->status);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    //partial delivery ends

    public function customerDetails($request)
    {
        $parcel = $this->all()->where('customer_phone_number', $request->phone_number)->first();

        if (!blank($parcel)):
            $data['customer_name'] = $parcel->customer_name;
            $data['customer_address'] = $parcel->customer_address;
        else:
            $data['customer_name'] = '';
            $data['customer_address'] = '';
        endif;

        return $data;
    }

    public function generate_random_string($length = 13)
    {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function trackParcel($id)
    {
        $parcel = $this->get($id);
        $response = $this->trackPaperflyParcel($parcel);

        if ($response->response_code == 200):
            return true;
        else:
            return $response->error->message;
        endif;
    }
}
