<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DeliveryTimeSet;
use Brian2694\Toastr\Facades\Toastr;

class DeliveryTimeSetController extends Controller
{
    public function index()
    {
        $setting = DeliveryTimeSet::first();
        return view('admin.delivery_time_set.index', compact('setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'working_hours_start' => 'nullable',
            'working_hours_end' => 'nullable',
            'offday' => 'nullable|array',
            'inside_city_days' => 'nullable|integer|min:0',
            'sub_city_days' => 'nullable|integer|min:0',
            'outside_city_days' => 'nullable|integer|min:0',
        ]);

        $offday = $request->has('offday') ? implode(',', $request->offday) : null;

        DeliveryTimeSet::updateOrCreate(
            ['id' => 1], // Always update the first record
            [
                'working_hours_start' => $request->working_hours_start,
                'working_hours_end' => $request->working_hours_end,
                'inside_city_days' => $request->inside_city_days,
                'sub_city_days' => $request->sub_city_days,
                'outside_city_days' => $request->outside_city_days,
                'offday' => $offday,
            ]
        );

        Toastr::success(__('delivery_time_updated_successfully'), __('success'));
        return redirect()->back();
    }
}
