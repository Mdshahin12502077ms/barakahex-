<?php

namespace App\Imports;

use App\Models\Merchant;
use App\Models\Parcel;
use App\Models\ParcelEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class ParcelsImport implements ToCollection, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithValidation, SkipsOnError
{
    use Importable, SkipsErrors;

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            Log::info('Processing row data: ' . $rows);

            $shop_id = request()->input('shop');
            // if (request()->input('merchant')) {
            //     if (request()->input('merchant')) {
            //         $merchant_id = request()->input('merchant');
            //     } else {
            //         $merchant_id = Sentinel::getUser()->merchant?->id;
            //     }
            // } else {
            //     $merchant_id = Sentinel::getUser()->merchant?->id;
            // }
            $merchant_id = request()->input('merchant') ?: (Sentinel::getUser()->merchant?->id);
            $user = jwtUser() ?? Sentinel::getUser();
            $unsafeChars = ['=', '+', '-', '@'];

            foreach ($rows as $index => $row) {
                Log::info('Processing row data: ' . $row);
                if ($user->user_type == 'merchant'):
                    $merchant = $user->merchant;
                elseif ($user->user_type == 'merchant_staff'):
                    $merchant = $user->staffMerchant;
                else:
                    $merchant = Merchant::find($merchant_id);
                endif;
                $fragile_charge = number_format(0, 2);
                $fragile = 0;
                $packaging_charge = number_format(0, 2);
                $packaging = 'no';
                if (isset($row['fragile']) && $row['fragile'] == 1) {
                    $fragile = 1;
                    $fragile_charge = settingHelper('fragile_charge');

                    if (isset($row['packaging']) && $row['packaging'] != 'no') {
                        $packaging = $row['packaging'];
                        $packaging_charge = settingHelper('package_and_charges')->where('id', $row['packaging'])->first()->charge;
                    }
                }
                // Excel parcel_type → DB canonical (user-facing: inside_city, outside_city match frontend "Inside City", "Outside City")
                $excelToCanonical = [
                    'inside_city' => 'same_day',
                    'outside_city' => 'sub_urban_area',
                    'sub_city' => 'sub_city',
                    'frozen' => 'frozen',
                    'third_party_booking' => 'third_party_booking',
                    'next_day' => 'next_day',
                ];
                $available_parcel_types = [];
                if ($user->user_type == 'merchant' || $user->user_type == 'merchant_staff'):
                    if (settingHelper('preferences')->where('title', 'same_day')->first()->merchant):
                        $available_parcel_types[] = 'inside_city';
                    endif;
                    if (settingHelper('preferences')->where('title', 'sub_city')->first()->merchant):
                        $available_parcel_types[] = 'sub_city';
                    endif;
                    if (settingHelper('preferences')->where('title', 'sub_urban_area')->first()->merchant):
                        $available_parcel_types[] = 'outside_city';
                    endif;
                else:
                    if (settingHelper('preferences')->where('title', 'same_day')->first()->staff):
                        $available_parcel_types[] = 'inside_city';
                    endif;
                    if (settingHelper('preferences')->where('title', 'sub_city')->first()->staff):
                        $available_parcel_types[] = 'sub_city';
                    endif;
                    if (settingHelper('preferences')->where('title', 'sub_urban_area')->first()->staff):
                        $available_parcel_types[] = 'outside_city';
                    endif;
                endif;
                $parcel_type_raw = $row['parcel_type'] ?? 'inside_city';
                if (!in_array($parcel_type_raw, $available_parcel_types)) {
                    throw new \Exception("Row " . ($index + 2) . ": " . __('parcel_type_not_available'));
                }
                $parcel_type = $excelToCanonical[$parcel_type_raw] ?? $parcel_type_raw;

                if ($parcel_type == "same_day" || $parcel_type == "next_day" || $parcel_type == "frozen"):
                    $location = 'inside_city';
                elseif ($parcel_type == "sub_city"):
                    $location = 'sub_city';
                elseif ($parcel_type == "sub_urban_area"):
                    $location = 'sub_urban_area';
                elseif ($parcel_type == "third_party_booking"):
                    $location = 'third_party_booking';
                endif;
                $weight = $row['weight'] ?? 1;
                $charge = data_get(
                    $merchant->charges,
                    $weight . '.' . $parcel_type
                );
                $cod_charge = data_get($merchant->cod_charges, $location);
                $vat = $merchant->vat ?? 0.00;
                $total_delivery_charge = $charge + $packaging_charge + $fragile_charge + ($row['price'] / 100 * $cod_charge);
                $total_delivery_charge += $total_delivery_charge / 100 * $vat;
                $payable = $row['price'] - $total_delivery_charge;
                Log::info(
                    'Total delivery charge: ' . $total_delivery_charge . ', Payable: ' . $payable,
                    [
                        'charge' => $charge,
                        'packaging_charge' => $packaging_charge,
                        'fragile_charge' => $fragile_charge,
                        'cod_charge' => $cod_charge,
                        'vat' => $vat,
                        'total_delivery_charge' => $total_delivery_charge,
                        'payable' => $payable
                    ]
                );
                if ($parcel_type == 'frozen') {
                    $pickup_date = date('Y-m-d');
                    $pickup_time = date('h:i:s');
                    $delivery_date = date("Y-m-d", strtotime('+2 hours', strtotime($pickup_date)));
                    $delivery_time = date("h:i:s", strtotime('+2 hours', strtotime($pickup_time)));
                } elseif ($parcel_type == 'same_day') {
                    if (date('H') >= settingHelper('pickup_accept_start') && date('H') <= settingHelper('pickup_accept_end')) {
                        $pickup_date = date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d'))));
                        $delivery_date = date("Y-m-d", strtotime('+1 days', strtotime(date('Y-m-d'))));
                    } else {
                        $pickup_date = date('Y-m-d');
                        $delivery_date = date("Y-m-d");
                    }
                } elseif ($parcel_type == 'sub_urban_area') {
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

                // Phone handling: normalize formats (Excel) then sanitize as in repository (ltrim unsafe chars)
                // $pickup_shop_phone_number = $row['pickup_shop_phone_number'] ?? '';
                // $customer_phone_number = $row['customer_phone_number'] ?? '';
                // if ($pickup_shop_phone_number != ''):
                //     $pickup_number = preg_replace('/^(\+880|880|0)/', '', $pickup_shop_phone_number);
                //     $pickup_number = preg_replace('/-/', '', $pickup_number);
                //     $row['pickup_shop_phone_number'] = '0' . $pickup_number;
                // endif;
                // if ($customer_phone_number):
                //     $pickup_number = preg_replace('/^(\+880|880|0)/', '', $customer_phone_number);
                //     $pickup_number = preg_replace('/-/', '', $pickup_number);
                //     $row['customer_phone_number'] = '0' . $pickup_number;
                // endif;
                $pickup_shop_phone_raw = $row['pickup_shop_phone_number'] ?? '';
                $customer_phone_raw = $row['customer_phone_number'] ?? '';
                $pickup_shop_phone_number = $pickup_shop_phone_raw;
                $customer_phone_number = $customer_phone_raw;
                if ($pickup_shop_phone_raw !== '') {
                    $norm = preg_replace('/^(\+880|880|0)/', '', $pickup_shop_phone_raw);
                    $norm = preg_replace('/-/', '', $norm);
                    $pickup_shop_phone_number = '0' . $norm;
                }
                if ($customer_phone_raw !== '') {
                    $norm = preg_replace('/^(\+880|880|0)/', '', $customer_phone_raw);
                    $norm = preg_replace('/-/', '', $norm);
                    $customer_phone_number = '0' . $norm;
                }
                $pickup_shop_phone_number = ltrim($pickup_shop_phone_number, implode('', $unsafeChars));
                $customer_phone_number = ltrim($customer_phone_number, implode('', $unsafeChars));

                $customer_name = ltrim((string) ($row['customer_name'] ?? ''), implode('', $unsafeChars));
                $customer_address = ltrim((string) ($row['customer_address'] ?? ''), implode('', $unsafeChars));
                $note = ltrim((string) ($row['note'] ?? ''), implode('', $unsafeChars));

                // Shop & branch resolution with fallback (match repository: default shop branch when shop has none)
                $shop = $shop_id ? $merchant->shops->where('id', $shop_id)->first() : null;
                if ($shop_id && ! $shop) {
                    throw new \Exception('Row ' . ($index + 2) . ': Shop with ID ' . $shop_id . ' not found for merchant.');
                }
                $defaultShop = $merchant->shops->where('default', true)->first();
                $resolved_shop_id = $shop_id ? (int) $shop_id : ($defaultShop ? $defaultShop->id : null);
                $pickup_branch_id = null;
                if ($shop_id && $shop) {
                    if (! empty($shop->pickup_branch_id)) {
                        $pickup_branch_id = $shop->pickup_branch_id;
                    } else {
                        $pickup_branch_id = $defaultShop ? $defaultShop->pickup_branch_id : null;
                    }
                } elseif ($defaultShop) {
                    $pickup_branch_id = $defaultShop->pickup_branch_id;
                }
                if (isset($row['pickup_branch']) && $row['pickup_branch'] !== '' && $row['pickup_branch'] !== null) {
                    $pickup_branch_id = $row['pickup_branch'];
                }
                $pickup_shop_phone_final = $pickup_shop_phone_number !== '' ? $pickup_shop_phone_number : ($shop ? $shop->shop_phone_number : '');
                $pickup_address_raw = $row['pickup_address'] ?? '';
                $pickup_address_final = $pickup_address_raw !== '' ? ltrim($pickup_address_raw, implode('', $unsafeChars)) : ($shop ? $shop->address : '');

                $parcelNo = make_unique_parcel_id();

                // 'customer_name' => $row['customer_name'],
                // 'customer_invoice_no' => $row['customer_invoice_no'] ?? (function () use ($merchant) {
                //     do {
                //         $inv = 'inv-' . $merchant->id . rand(1000, 9999); } while (Parcel::where('customer_invoice_no', $inv)->exists());
                //     return $inv;
                // })(),
                // 'customer_phone_number' => $row['customer_phone_number'],
                // 'customer_address' => $row['customer_address'],
                // 'note' => $row['note'] ?? '',
                // 'pickup_shop_phone_number' => $row['pickup_shop_phone_number'] ?? $merchant->shops->where('id', $shop_id)->first()->shop_phone_number,
                // 'pickup_address' => $row['pickup_address'] ?? $merchant->shops->where('id', $shop_id)->first()->address,
                // 'pickup_branch_id' => $row['pickup_branch'] ?? ($merchant->shops->where('id', $shop_id)->first()->pickup_branch_id != '' ? $merchant->shops->where('id', $shop_id)->first()->pickup_branch_id : null),
                // 'shop_id' => $merchant->shops->where('id', $shop_id)->first()->id,
                $parcel = Parcel::create([
                    'parcel_no' => $parcelNo,
                    'merchant_id' => $merchant->id,
                    'short_url' => url('/tracking/' . $parcelNo),
                    'price' => $row['price'],
                    'selling_price' => $row['selling_price'] ?? 0,
                    'customer_name' => $customer_name,
                    'customer_invoice_no' => $row['customer_invoice_no'] ?? (function () use ($merchant) {
                        do {
                            $inv = 'inv-' . $merchant->id . rand(1000, 9999);
                        } while (Parcel::where('customer_invoice_no', $inv)->exists());
                        return $inv;
                    })(),
                    'customer_phone_number' => $customer_phone_number,
                    'customer_address' => $customer_address,
                    'note' => $note,

                    // Charge
                    'packaging' => $packaging,
                    'packaging_charge' => $packaging_charge,
                    'fragile' => $fragile,
                    'fragile_charge' => $fragile_charge,
                    'open_box' => $row['open_box'] ?? 0,
                    'home_delivery' => $row['home_delivery'] ?? 0,

                    'weight' => $weight,
                    'parcel_type' => $parcel_type,
                    'charge' => $charge,
                    'cod_charge' => $cod_charge,
                    'vat' => $vat,
                    'total_delivery_charge' => floor($total_delivery_charge),
                    'payable' => ceil($payable),
                    'location' => $location,
                    // End charge

                    // pickup shop details
                    'pickup_shop_phone_number' => $pickup_shop_phone_final,
                    'pickup_address' => $pickup_address_final,
                    'pickup_branch_id' => $pickup_branch_id,
                    'shop_id' => $resolved_shop_id,
                    'pickup_date' => $pickup_date,
                    'date' => date('Y-m-d'),
                    'pickup_time' => $pickup_time ?? '',
                    'delivery_date' => $delivery_date ?? '',
                    'delivery_time' => $delivery_time ?? '',
                    'user_id' => $user->id,
                ]);
                if (!$parcel) {
                    throw new \Exception(__('there is some error in your import file at row ') . ($index + 2) . ', parcel creation failed');
                }
                ParcelEvent::create([
                    'parcel_id' => $parcel->id,
                    'user_id' => $user->id,
                    'title' => 'parcel_create_event',
                ]);

            }
            DB::commit();
        } catch (\Exception $e) {

            // Handle the exception here
            Log::error('Exception caught', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            // Rollback the transaction
            DB::rollBack();
            // Optionally, you can rethrow the exception if needed
            throw $e;
        }
    }

    public function rules(): array
    {
        $user = jwtUser() ?? Sentinel::getUser();

        if ($user->user_type == 'merchant' || $user->user_type == 'merchant_staff'):
            return [
                '*.price' => 'required|numeric',
                '*.selling_price' => 'required|numeric',
                '*.customer_name' => 'required|string|max:100',
                '*.parcel_type' => 'string|nullable',
                // '*.customer_invoice_no' => 'required',
                '*.customer_phone_number' => ['required', 'digits:11', 'regex:/^[0-9]{11}$/'],
                '*.customer_address' => 'required|string',

            ];
        else:
            return [
                '*.price' => 'required|numeric',
                '*.selling_price' => 'required|numeric',
                '*.customer_name' => 'required|string|max:100',
                '*.parcel_type' => 'string|nullable',
                // '*.customer_invoice_no' => 'required',
                '*.customer_phone_number' => ['required', 'digits:11', 'regex:/^[0-9]{11}$/'],
                '*.customer_address' => 'required|string',
            ];
        endif;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
