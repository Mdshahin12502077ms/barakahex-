<?php

namespace App\Repositories\Admin;

use App\Models\Charge;
use App\Models\CodCharge;
use App\Models\PackageAndCharge;
use App\Models\Setting;
use App\Models\Merchant;
use App\Repositories\Interfaces\Admin\SettingInterface;
use Illuminate\Support\Facades\DB;

class SettingRepository implements SettingInterface
{

    public function store($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->except('_token') as $key => $value):
                $setting = Setting::where('title', $key)->first();
                if ($setting == "" || $setting == null):
                    $setting = new Setting();
                    $setting->title = $key;
                    $setting->value = $value;
                else:
                    $setting->value = $value;
                endif;
                $setting->save();
            endforeach;

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function arrayStore($request)
    {
        DB::beginTransaction();
        try {
            $array = [];
            $title = '';

            if (isset($request->packaging_types)):
                $title = 'package_types_and_charge';
                foreach ($request->packaging_types as $key => $type):
                    if (!blank($type) && !blank($request->charges[$key])):
                        $array += [$type => $request->charges[$key]];
                    endif;
                endforeach;
            endif;

            $setting = Setting::where('title', $title)->first();

            if ($title != ''):
                if ($setting == "" || $setting == null):
                    $setting = new Setting();
                    $setting->title = $title;
                    $setting->array_value = $array;
                else:
                    $setting->array_value = $array;
                endif;

                $setting->save();
            endif;

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function packingCharge()
    {
        try {
            $package_and_charges = PackageAndCharge::all();
            return $package_and_charges;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deletePackagingCharge($id)
    {
        DB::beginTransaction();
        try {
            PackageAndCharge::find($id)->delete();
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function packingChargeAdd()
    {
        DB::beginTransaction();
        try {
            $package_charge = new PackageAndCharge();
            $package_charge->save();
            DB::commit();
            return $package_charge->id;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function packagingChargeUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->packaging_types as $key => $type):
                $old_record = PackageAndCharge::where('package_type', $type)->where('id', '!=', $request->ids[$key])->first();
                if (blank($old_record) && $type != ''):
                    $table = PackageAndCharge::find($request->ids[$key]);
                    if (blank($table)):
                        $table = new PackageAndCharge();
                    endif;
                    $table->package_type = $type;
                    $table->charge = $request->charges[$key];
                    $table->save();
                elseif (($type == '' || $old_record) && $request->ids[$key] != ''):
                    $this->deletePackagingCharge($request->ids[$key]);
                endif;
            endforeach;
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function chargeUpdate($request)
    {
        DB::beginTransaction();
        try {
            $charge = '';
            foreach ($request->weights as $key => $weight) {
                if (!$request->cod_ids[$key]) {
                    $charge = new Charge;
                } else {
                    $charge = Charge::find($request->cod_ids[$key]);
                }

                $charge->weight = $weight;
                $charge->same_day = $request->same_day[$key];
                //  $charge->next_day = $request->next_day[$key];
                $charge->sub_city = $request->sub_city[$key];
                $charge->sub_urban_area = $request->sub_urban_area[$key];
                $charge->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return false;
        }
    }

}
