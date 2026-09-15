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
        ]);

        $offday = $request->has('offday') ? implode(',', $request->offday) : null;

        DeliveryTimeSet::updateOrCreate(
            ['id' => 1], // Always update the first record
            [
                'working_hours_start' => $request->working_hours_start,
                'working_hours_end' => $request->working_hours_end,
                'offday' => $offday,
            ]
        );

        Toastr::success(__('delivery_time_updated_successfully'), __('success'));
        return redirect()->back();
    }
}
