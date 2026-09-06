<?php

namespace App\Http\Controllers\Admin\WebsiteSetting;

use App\Http\Controllers\Controller;
use App\Models\HomeScreen;
use App\Repositories\LanguageRepository;
use App\Repositories\PageRepository;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;
use App\Traits\ImageTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class WebsiteSettingController extends Controller
{
    use ImageTrait;

    protected $setting;

    protected $language;

    public function __construct(SettingRepository $setting, LanguageRepository $language)
    {
        $this->setting  = $setting;
        $this->language = $language;
    }


    public function themeOptions(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $lang          = $request->site_lang ? $request->site_lang : App::getLocale();

        return view('admin.website.theme_options', compact('lang'));
    }

    public function updateThemesOptions(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        try {
            $this->setting->update($request);


            Artisan::call('storage:link');

            DB::commit();

            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => __('something_went_wrong_please_try_again.'),
            ];

            return response()->json($data);
        }
    }

    public function menuSection(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $lang = $request->site_lang ? $request->site_lang : App::getLocale();

        return view('admin.website.menu', compact('lang'));
    }

    public function updateMenuSection(Request $request): \Illuminate\Http\JsonResponse
    {

        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        try {

            $this->setting->update($request);


            Artisan::call('storage:link');

            DB::commit();

            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => __('something_went_wrong_please_try_again.'),
            ];

            return response()->json($data);
        }
    }

    public function sectionTitleSubtitle(Request $request)
    {
        $lang = $request->site_lang ? $request->site_lang : App::getLocale();

        return view('admin.website.section_title_subtitle', compact('lang'));
    }
    public function updateSectionTitleSubtitle(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }
        try {
            $this->setting->update($request);
            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => __('something_went_wrong_please_try_again.'),
            ];

            return response()->json($data);
        }
    }

    public function seo(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $data = [
                'languages' => $this->language->all(),
                'lang'      => $request->lang == '' ? app()->getLocale() : $request->lang,
            ];

            return view('admin.website.seo', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function saveSeoSetting(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }
        try {
            $this->setting->update($request);
            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => __('something_went_wrong_please_try_again.'),
            ];

            return response()->json($data);
        }
    }

    public function google(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            return view('admin.website.google_setup');
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function saveGoogleSetup(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }
        $request->validate([
            'tracking_code'      => 'required_if:is_google_analytics_activated,==,1',
            'recaptcha_site_key' => 'required_if:is_recaptcha_activated,==,1',
            'recaptcha_secret'   => 'required_if:is_recaptcha_activated,==,1',
        ]);
        try {
            $this->setting->update($request);
            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => __('something_went_wrong_please_try_again.'),
            ];

            return response()->json($data);
        }
    }

    public function customCss(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            return view('admin.website.custom_css');
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function customJs(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            return view('admin.website.custom_js');
        } catch (\Exception $e) {
            Toastr::error(__('something_went_wrong_please_try_again.'));

            return back();
        }
    }

    public function saveCustomCssAndJs(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }
        try {
            $this->setting->update($request);
            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => __('something_went_wrong_please_try_again.'),
            ];

            return response()->json($data);
        }
    }

    public function fbPixel(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            return view('admin.website.fb_pixel');
        } catch (\Exception $e) {
            Toastr::error(__('something_went_wrong_please_try_again.'));

            return back();
        }
    }

    public function saveFbPixel(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }
        $request->validate([
            'facebook_pixel_id' => 'required_if:is_facebook_pixel_activated,==,1',
        ]);
        try {
            $this->setting->update($request);
            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => __('something_went_wrong_please_try_again.'),
            ];

            return response()->json($data);
        }
    }

    public function headerFooter(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.website.header_footer');
    }

    public function updateHeaderFooter(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        try {
            $this->setting->update($request);
            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => __('something_went_wrong_please_try_again.'),
            ];

            return response()->json($data);
        }
    }

    public function heroSection(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $languages     = app('languages');
        $lang          = $request->site_lang ? $request->site_lang : App::getLocale();
        return view('admin.website.hero', compact('languages', 'lang'));
    }

    public function updateHeroSection(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        try {
            $this->setting->update($request);
            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => __('something_went_wrong_please_try_again.'),
            ];

            return response()->json($data);
        }
    }


    public function ctaSection(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $lang          = $request->site_lang ? $request->site_lang : App::getLocale();
        return view('admin.website.cta', compact('lang'));
    }

    public function updateCTASection(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        try {
            $this->setting->update($request);
            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => __('something_went_wrong_please_try_again.'),
            ];

            return response()->json($data);
        }
    }

    public function statisticSection(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $lang          = $request->site_lang ? $request->site_lang : App::getLocale();
        return view('admin.website.statistic', compact('lang'));
    }

    public function updateStatisticSection(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        try {
            $this->setting->update($request);
            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => __('something_went_wrong_please_try_again.'),
            ];

            return response()->json($data);
        }
    }
    public function contactSection(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $lang          = $request->site_lang ? $request->site_lang : App::getLocale();
        return view('admin.website.contact', compact('lang'));
    }

    public function updateContactSection(Request $request)
    {

        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }

        try {
            $this->setting->update($request);

            Toastr::success(__('update_successful'));
            return back();
        } catch (\Exception $e) {

            Toastr::error(__('something_went_wrong.please_try_again.'));
            return back();
        }
    }

    public function pricingSection(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $lang          = $request->site_lang ? $request->site_lang : App::getLocale();
        return view('admin.website.pricing', compact('lang'));
    }

    public function updatePricingSection(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        try {
            $this->setting->update($request);
            Toastr::success(__('update_successful'));
            $data = [
                'success' => __('update_successful'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'error' => $e->getMessage(),
            ];

            return response()->json($data);
        }
    }


}
