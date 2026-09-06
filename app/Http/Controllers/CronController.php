<?php

namespace App\Http\Controllers;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class CronController extends Controller
{
    public function cron()
    {
        try {
            $outputDaily        = Artisan::call('daily:payment-request');
            $outputWeekly       = Artisan::call('weekly:payment-request');
            $outputMonthly      = Artisan::call('monthly:payment-request');
            return redirect()->back()->with('success', __('cron_job_executed_successfully'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', __('something_went_wrong_please_try_again'));
        }
    }


}
