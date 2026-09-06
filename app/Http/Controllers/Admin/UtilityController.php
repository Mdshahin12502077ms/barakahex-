<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\UpdateTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    use UpdateTrait;

    public function serverInfo(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.utility.server_info');
    }

    public function systemUpdate(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $fields   = [
                'item_id'         => '55375587',
                'purchase_code'   => setting('purchase_code'),
                'current_version' => setting('current_version'),
            ];
            $response = false;
            if (env('DEV_MODE') == 'on') {
                $url = 'https://license.spagreen.net/version-check-including-beta';
            } else {
                $url = 'https://license.spagreen.net/version-check';
            }
            $request  = curlRequest($url, $fields);
            if (property_exists($request, 'status') && $request->status) {
                $response = $request->release_info;
            }

            if (is_bool($response)) {
                $latest_version    = setting('current_version');
                $current_int       = (int) setting('current_version');
                $latest_int        = (int) $latest_version;
                $is_old            = $current_int < $latest_int;
                $next_version_code = 'v'.implode('.', str_split((string) $current_int));
                $next_version      = $current_int;
            } else {
                $latest_version    = $response->version;
                $current_int       = (int) setting('current_version');
                $latest_int        = (int) $latest_version;
                $is_old            = $current_int < $latest_int;
                $next_version      = $latest_int;
                $next_version_code = 'v'.implode('.', str_split((string) $next_version));
            }

            $data     = [
                'response'          => $response,
                'latest_version'    => setting('current_version'),
                'is_old'            => $is_old,
                'next_version'      => $next_version,
                'next_version_code' => $next_version_code,
            ];

            return view('admin.utility.system_update', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function downloadUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (config('app.demo_mode')) {
                return response()->json([
                    'message' => __('This function is disabled in demo server.'),
                    'type'    => __('Error').' !',
                    'class'   => 'danger',
                ]);
            }

            $update = $this->downloadUpdateFile($request->all());

            if (is_string($update)) {
                return response()->json([
                    'message' => $update,
                    'type'    => __('Error').' !',
                    'class'   => 'danger',
                ]);
            }

            return response()->json([
                'type'    => __('Success').' !',
                'class'   => 'success',
                'message' => __('Update Successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'type'    => __('Error').' !',
                'class'   => 'danger',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
