<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Charge;
use App\Repositories\Admin\WebsitePartnerLogoRepository;
use App\Repositories\Admin\WebsiteNewsAndEventRepository;
use App\Repositories\Admin\WebsiteServiceRepository;
use App\Repositories\Admin\WebsiteAboutRepository;
use App\Repositories\Admin\WebsiteFeatureRepository;
use App\Repositories\Admin\WebsiteTestimonialRepository;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    protected $partner;
    protected $news_and_event;
    protected $about;
    protected $service;
    protected $feature;
    protected $testimonial;


    public function __construct(WebsitePartnerLogoRepository $partner, WebsiteNewsAndEventRepository $news_and_event, WebsiteAboutRepository $about, WebsiteServiceRepository $service, WebsiteFeatureRepository $feature, WebsiteTestimonialRepository $testimonial)
    {
        $this->partner                  = $partner;
        $this->news_and_event           = $news_and_event;
        $this->about                    = $about;
        $this->service                  = $service;
        $this->feature                  = $feature;
        $this->testimonial              = $testimonial;


    }
    public function index(Request $request)
    {
        $languages        = app('languages');
        $lang             = $request->site_lang ? $request->site_lang : App::getLocale();
        $menu_quick_link  = headerFooterMenu('footer_quick_link_menu', $lang);
        $menu_useful_link = headerFooterMenu('footer_useful_link_menu');

        $data             = [

            'menu_quick_links'  => $menu_quick_link,
            'menu_useful_links' => $menu_useful_link,
            'lang'              => $request->lang ?? app()->getLocale(),
            'menu_language'     => headerFooterMenu('header_menu', $lang),
            'partner_logos'     => $this->partner->all(),
            'events'            => $this->news_and_event->all(),
            'abouts'            => $this->about->all(),
            'services'          => $this->service->all(),
            'features'          => $this->feature->all(),
            'testimonials'      => $this->testimonial->all(),
            'charges'           => Charge::all(),

        ];
        return view('website.page.home', $data);
    }

    public function chargeDetails(Request $request)
    {
        $data             = [];
        $packaging_charge = number_format(0, 2);

        if ($request->packaging != null) {
            $packaging = settingHelper('package_and_charges')->where('id', $request->packaging)->first();
            if ($packaging) {
                $packaging_charge = $packaging->charge ?? 0;
            }
        }

        $fragile_charge = number_format(0, 2);
        if ($request->fragile == 1) {
            $fragile_charge = settingHelper('fragile_charge') ?? 0;
        }

        if ($request->day == "same_day") {
            $parcel_type    = 'same_day';
            $location       = 'inside_city';
        } elseif ($request->day == "next_day") {
            $parcel_type    = 'next_day';
            $location       = 'sub_city';
        } elseif ($request->city == "sub_city") {
            $parcel_type    = 'sub_city';
            $location       = 'sub_city';
        } elseif ($request->city == "sub_urban_area") {
            $parcel_type    = 'sub_urban_area';
            $location       = 'sub_urban_area';
        } else {
        }

        $foundCharge = 0;
        if (isset($parcel_type)) {
            $system_charges = Charge::where('weight', $request->weight)->first();
            if ($system_charges) {
                $foundCharge = $system_charges[$parcel_type] ?? 0;
            }
        }

        $total_charge   = $foundCharge + $packaging_charge + $fragile_charge;
        $data['charge'] = number_format($total_charge, 2);

        return $data;
    }

    public function cacheClear()
    {
        try {
            Artisan::call('all:clear');
            Artisan::call('migrate', ['--force' => true]);
            Toastr::success(__('cache_cleared_successfully'));

            return back();
        } catch (\Exception $e) {
            // dd($e->getMessage());
            Toastr::error('something_went_wrong_please_try_again', 'Error!');

            return back();
        }
    }

    public function changeLanguage($locale): \Illuminate\Http\RedirectResponse
    {
        cache()->get('locale');
        app()->setLocale($locale);

        return redirect()->back();
    }


}
