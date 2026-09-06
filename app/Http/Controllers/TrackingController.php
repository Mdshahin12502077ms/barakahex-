<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parcel;
use App\Models\Charge;
use App\Models\CodCharge;
use App;

class TrackingController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $languages          = app('languages');
        $lang               = $request->site_lang ? $request->site_lang : App::getLocale();
        $menu_quick_link    = headerFooterMenu('footer_quick_link_menu', $lang);
        $parcel             = null;
        $noParcelFound      = false;

        if ($id !== null) {
            $parcel             = Parcel::where('parcel_no', $id)->first();
            if (!$parcel) {
                $noParcelFound  = true;
            }
        }

        $data = [
            'menu_quick_links'  => $menu_quick_link,
            'lang'              => $request->lang ?? app()->getLocale(),
            'menu_language'     => headerFooterMenu('header_menu', $lang),
            'parcel'            => $parcel,
            'noParcelFound'     => $noParcelFound,
        ];

        return view('website.page.tracking', $data);
    }


    public function tracking(Request $request)
    {
        $parcel     = Parcel::where('parcel_no', $request->parcelNo)->first();
        if ($parcel && $parcel->events) {
            $view   = view('website.tracking_section._tracking', compact('parcel'))->render();
            return response()->json($view);
        } else {
            return response()->json('<p>No events found for this tracking number.</p>', 404);
        }
    }

}
