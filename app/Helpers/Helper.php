<?php

    use Carbon\Carbon;
    use App\Models\Parcel;
    use App\Models\Setting;
    use App\Models\Preference;
    use Illuminate\Support\Str;
    use App\Models\EmailTemplate;
    use App\Models\PackageAndCharge;
    use Tymon\JWTAuth\Facades\JWTAuth;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Mail;
    use GeoSot\EnvEditor\Facades\EnvEditor;
    use Illuminate\Support\Facades\Request;
    use Illuminate\Support\Facades\Storage;

    if (!function_exists('isLocalhost')) {
        function isLocalhost(): string
        {
            return public_path('/') . '';
        }
    }

    if (!function_exists('curlRequest')) {
        function curlRequest($url, $fields, $method = 'POST', $headers = [], $is_array = false)
        {
            $client = new \GuzzleHttp\Client(['verify' => false]);
            if (is_string($fields)) {
                $data = [
                    'body' => $fields,
                    'headers' => $headers,
                ];
            } else {
                $data = [
                    'form_params' => $fields,
                    'headers' => $headers,
                ];
            }
            $response = $client->request($method, $url, $data);
            $result = $response->getBody()->getContents();
            return json_decode($result, $is_array);
        }
    }

    if (!function_exists('httpRequest')) {
        function httpRequest($url, $fields, $headers = [], $is_form = false, $method = 'POST')
        {
            if ($is_form) {
                $response = Http::withHeaders($headers)->asForm()->$method($url, $fields);
            } else {
                $response = Http::withHeaders($headers)->$method($url, $fields);
            }
            return $response->json();
        }
    }

    if (!function_exists('get_media')) {
        function get_media($item, $storage = 'local', $updater = false)
        {
            if (!blank($item) && !blank($storage)) {
                if ($storage == 'local') {
                    if ($updater) {
                        return public_path($item);
                    } else {
                        return asset($item);
                    }
                } elseif ($storage == 'aws_s3') {
                    return Storage::disk('s3')->url($item);
                } elseif ($storage == 'wasabi') {
                    return Storage::disk('wasabi')->url($item);
                }
            }

            return false;
        }
    }

    if (!function_exists('static_asset')) {
        function static_asset($path = null, $secure = null)
        {
            return asset($path, $secure);
        }
    }

    if (!function_exists('isLocalhost')) {
        function isLocalhost(): string
        {
            return !(str_contains(php_sapi_name(), 'cli') || defined('LARAVEL_START_FROM_PUBLIC')) ? 'public/' : '';
        }
    }


    if (!function_exists('get_price')) {
        function get_price($price, $curr = null): ?string
        {
            return format_price(convert_price($price, $curr), $curr);
        }
    }


    if (!function_exists('convert_price')) {
        function convert_price($price, $curr = null): float|int
        {
            $exchange_rate = 1;
            $currencies = app('currencies');
            if (!$curr) {
                $curr = setting('default_currency');
            }
            $currency = $currencies->where('code', $curr)->first();
            if ($currency) {
                $exchange_rate = $currency->exchange_rate;
            }
            return floatval($price) * floatval($exchange_rate);
        }
    }


    if (!function_exists('get_symbol')) {
        function get_symbol($curr = null)
        {
            $curr = setting('default_currency') ?? '$';
            return $curr;
        }
    }


    if (!function_exists('addon_is_activated')) {
        function addon_is_activated($addon_unique_identity)
        {
            $addon = \app('addons')->where('addon_identifier', $addon_unique_identity)->first();
            return isset($addon);
        }
    }

    if (!function_exists('envWrite')) {
        function envWrite($key, $value)
        {
            try {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                } else {
                    $value = '"' . trim($value) . '"';
                }
                if (EnvEditor::keyExists($key)) {
                    EnvEditor::editKey($key, $value);
                } else {
                    EnvEditor::addKey($key, $value);
                }
            } catch (Exception $e) {
                dd($e);
            }
        }
    }

    if (!function_exists('priceFormatUpdate')) {
        function priceFormatUpdate($price, $curr, $type = null): float|int
        {
            if (!$price) {
                $price = 0;
            }
            $active_currency = \app('currencies')->where('id', $curr)->first();
            $rate = $active_currency ? $active_currency->exchange_rate : 1;
            if ($type == '*') {
                return round($price * $rate, setting('no_of_decimals'));
            } else {
                return $price / $rate;
            }
        }
    }

    if (!function_exists('headerFooterMenu')) {

        function headerFooterMenu($title, $lang = 'en')
        {
            try {
                $settings = app('settings');
                if (in_array($title, get_yrsetting('setting_array')) || in_array($title, get_yrsetting('setting_by_lang'))) {
                    $data = $settings->where('title', $title)->where('lang', $lang)->first();
                    if(!$data) {
                        $data = $settings->where('title', $title)->where('lang', 'en')->first();
                    }
                    if (!blank($data)) {
                        return $data->value ? unserialize($data->value) : [];
                    }
                }
            } catch (\Exception $e) {
                return '';
            }
        }
    }


    if (!function_exists('isHome')) {
        function isHome()
        {
            if (request()->path() == '/' || request()->path() == 'home1' || request()->path() == 'home2' || request()->path() == 'home3' || request()->path() == App::getLocale() || request()->path() == setting('default_language')) {
                return true;
            } else {
                return false;
            }
        }
    }


    if (!function_exists('authUser')) {
        function authUser()
        {
            return auth()->check() ? auth()->user() : jwtUser();
        }
    }
    if (!function_exists('userCurrency')) {
        function userCurrency()
        {
            $currency_id = setting('default_currency');
            if (auth()->check()) {
                $currency_id = auth()->user()->currency_code;
            } elseif (session()->has('currency_code')) {
                $currency_id = session()->get('currency_code');
            }

            return $currency_id;
        }
    }


    if (!function_exists('userLanguage')) {
        function userLanguage()
        {
            $locale = setting('default_language');
            if (auth()->check()) {
                $locale = auth()->user()->lang;
            } elseif (session()->has('currency')) {
                $locale = session()->get('lang');
            }

            return $locale;
        }
    }

    if (!function_exists('stringMasking')) {
        function stringMasking($string, $pattern, $start_range = null, $end_range = null)
        {
            return isDemoMode() ? \Illuminate\Support\Str::mask($string, $pattern, $start_range, $end_range) : $string;
        }
    }

    if (!function_exists('settingHelper')) {
        function settingHelper($title)
        {
            if ($title == 'package_and_charges'):
                $package_and_charges = PackageAndCharge::get();
                return $package_and_charges;
            elseif ($title == 'preferences'):
                $preferences = Preference::get();
                return $preferences;

            elseif ($title == 'delivery_otp'):
                $otp_preferences = Setting::where('title', 'delivery_otp')->first();
                return $otp_preferences;

            else:
                $data = Setting::where('title', $title)->first();
                if (!blank($data)):
                    return $data->value;
                else:
                    return '';
                endif;
            endif;
        }
    }

    if (!function_exists('getSlug')) {
        function getSlug($table, $name, $column = 'slug', $id = null): string
        {
            $slug = \Illuminate\Support\Str::slug($name);
            $count = \Illuminate\Support\Facades\DB::table($table)->when($id, function ($query) use ($id) {
                $query->where('id', '!=', $id);
            })->where($column, $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . strtolower(\Illuminate\Support\Str::random(5));
            }

            return $slug;
        }
    }

    if (!function_exists('make_unique_parcel_id')) :
        function make_unique_parcel_id()
        {

            $start = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
            $end = Carbon::now()->format('Y-m-d H:i:s');
            $mto = Parcel::withoutGlobalScopes()->whereBetween('created_at', [$start, $end])
                    ->count() + 1;

            $ddmmyy = date('dmy');

            $tto_startDate = Carbon::now()->format('Y-m-d 00:00:00');
            $tto_endDate = Carbon::now()->format('Y-m-d 23:59:59');
            $tto = Parcel::withoutGlobalScopes()->whereBetween('created_at', [$tto_startDate, $tto_endDate])
                    ->count() + 1;
            return date('mdhis') . $mto . $tto;
        }
    endif;

    if (!function_exists('isDemoMode')) {

        function isDemoMode(): bool
        {
            return config('app.demo_mode');
        }
    }

    if (!function_exists('isInstalled')) {
        function isInstalled(): bool
        {
            return config('app.app_installed');
        }
    }

    if (!function_exists('is_file_exists')) {
        function is_file_exists($item, $storage = 'local')
        {
            if (!blank($item) && !blank($storage)) {
                if ($storage == 'local') {
                    if (file_exists(public_path($item)) || file_exists(base_path('public/' . $item)) || file_exists(base_path($item))) {
                        return true;
                    }
                } elseif ($storage == 'aws_s3') {
                    if (Storage::disk('s3')->exists($item)) {
                        return true;
                    }
                } elseif ($storage == 'wasabi') {
                    if (Storage::disk('wasabi')->exists($item)) {
                        return true;
                    }
                }
            }

            return false;
        }
    }

    if (!function_exists('user_curr')) {
        function user_curr()
        {
            if (addon_is_activated('ishopet')) {
                $user = auth()->user();
                return $user->currency_code;
            }
            return null;
        }
    }

    if (!function_exists('format_price')) {
        function format_price($price, $curr = null)
        {
            $no_of_decimals = setting('no_of_decimals') ?? 2;
            $decimal_separator = setting('decimal_separator') ? setting('decimal_separator') : '.';
            $thousands_separator = $decimal_separator == ',' ? '.' : ',';
            $currency_symbol_format = setting('currency_symbol_format') ? setting('currency_symbol_format') : 'amount_symbol';
            if ($no_of_decimals != '') {
                $price = number_format($price, $no_of_decimals, $decimal_separator, $thousands_separator);
            } else {
                $price = number_format($price, 2, $decimal_separator, $thousands_separator);
            }
            $currency_postion = setting('currency_postion');
            if ($currency_postion == 'before') {
                return get_symbol($curr) . $price;
            } else {
                return $price . get_symbol($curr);
            }
        }
    }


    if (!function_exists('fontURL')) {
        function fontURL()
        {
            $fonts_url = static_asset('fonts/poppins/css.css');
            $font_title = setting('fonts');
            $font_title_sl = preg_replace('/\s+/', '_', strtolower($font_title));
            if (File::exists(public_path('fonts/' . $font_title_sl . '/css.css'))) {
                $fonts_url = static_asset('fonts/' . $font_title_sl . '/css.css');
            }
            return $fonts_url;
        }
    }
    if (!function_exists('getFileName')) {
        function getFileName($file)
        {
            $name = '';
            if ($file) {
                $file = explode('/', $file);
                $name = $file[count($file) - 1];
            }

            return $name;
        }
    }


    if (!function_exists('nullCheck')) {
        function nullCheck($value)
        {
            return $value ?: '';
        }
    }

    if (!function_exists('currencyCheck')) {
        function currencyCheck()
        {
            if (session()->has('currency')) {
                $currency = session()->get('currency');
            } elseif (setting('default_currency')) {
                $currency = setting('default_currency');
            } else {
                $currency = '$';
            }

            return $currency;
        }
    }

    if (!function_exists('arrayCheck')) {
        function arrayCheck($key, $array): bool
        {
            return is_array($array) && count($array) > 0 && array_key_exists($key, $array) && !empty($array[$key]) && $array[$key] != 'null';
        }
    }

    if (!function_exists('isAppMode')) {
        function isAppMode(): bool
        {
            return config('app.mobile_mode') == 'on';
        }
    }

    function hasPermission($key_word)
    {
        if (in_array($key_word, \Sentinel::getUser()->permissions)) {
            return true;
        }
        return false;
    }


    function hasNotification($key_word, $userPermission)
    {
        if (in_array($key_word, $userPermission)) {
            return true;
        }
        return false;
    }

    if (!function_exists('jwtUser')) {
        function jwtUser()
        {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (\Exception $e) {
                return null;
            }

            return $user;
        }
    }

    if (!function_exists('get_yrsetting')) {

        function get_yrsetting($setting_for)
        {
            return config()->get('lmssetting.' . $setting_for);
        }
    }

    if (!function_exists('setting')) {
        function setting($title, $lang = 'en')
        {
            if (!$lang) {
                $lang = app()->getLocale();
            }
            try {
                $settings = app('settings');
                if (!blank($title)) {
                    if (in_array($title, get_yrsetting('setting_array')) || in_array($title, get_yrsetting('setting_image'))) {
                        $data = $settings->where('title', $title)->first();
                        if (!blank($data)) {
                            return $data->value ? unserialize($data->value) : [];
                        }
                    } else {
                        if (in_array($title, get_yrsetting('setting_by_lang'))) {
                            $data = $settings->where('title', $title)->where('lang', $lang)->first();
                            if (blank($data)) {
                                $data = $settings->where('title', $title)->where('lang', 'en')->first();
                                return !blank($data) ? $data->value : '';
                            }
                            return $data->value;
                        } else {
                            $data = $settings->where('title', $title)->first();
                        }
                        return !blank($data) ? $data->value : '';
                    }
                } else {
                    return '';
                }
            } catch (\Exception $e) {
                // dd($e);
                return '';
            }
        }
    }

    if (!function_exists('checkEmptyProvider')) {
        function checkEmptyProvider($check_for)
        {
            foreach (get_yrsetting($check_for) as $title) {
                if (setting($title) == '') {
                    return false;
                }
            }
            return true;
        }
    }

    if (!function_exists('formatBytes')) {

        function formatBytes($size, $precision = 2)
        {
            $base = log($size, 1024);
            $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

            return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
        }
    }
    if (!function_exists('EmailTemplate')) {
        function EmailTemplate($title)
        {
            return EmailTemplate::where('title', $title)->first();
        }
    }

    if (!function_exists('currencyList')) {
        function currencyList()
        {
            $currency_list = [];

            if (cache()->get('currency_list')) {
                $currency_list = cache()->get('currency_list');
            } else {
                $file = file_get_contents(public_path('sql/currencies.json'));
                $data = json_decode($file, true);
                foreach ($data as $key => $value) {
                    $currency_list[$key] = $key;
                }
                cache()->put('currency_list', $currency_list, now()->addDays(5));
            }

            return $currency_list;
        }
    }


    if (!function_exists('getFileLink')) {
        function getFileLink($size, $array, $offline = null)
        {
            if (is_string($array)) {
                $decoded = json_decode($array, true);
                if (is_array($decoded)) {
                    $array = $decoded;
                }
            }

            if (!is_array($array) || empty($array)) {
                return static_asset('images/default/' . $size . '.png');
            }

            $storage = $array['storage'] ?? 'local';

            if (isset($array['image_' . $size]) && !blank($array['image_' . $size])) {
                if (@is_file_exists($array['image_' . $size], $storage)) {
                    return get_media($array['image_' . $size], $storage);
                }
            }

            $lowerSize = strtolower($size);
            if (isset($array['image_' . $lowerSize]) && !blank($array['image_' . $lowerSize])) {
                if (@is_file_exists($array['image_' . $lowerSize], $storage)) {
                    return get_media($array['image_' . $lowerSize], $storage);
                }
            }

            if (isset($array[$size]) && !blank($array[$size])) {
                if (@is_file_exists($array[$size], $storage)) {
                    return get_media($array[$size], $storage);
                }
            }

            if (isset($array['original_image']) && !blank($array['original_image'])) {
                if (@is_file_exists($array['original_image'], $storage)) {
                    return get_media($array['original_image'], $storage);
                }
            }

            foreach ($array as $key => $val) {
                if (is_string($val) && (str_ends_with(strtolower($val), '.jpg') || str_ends_with(strtolower($val), '.jpeg') || str_ends_with(strtolower($val), '.png') || str_ends_with(strtolower($val), '.webp'))) {
                    if (@is_file_exists($val, $storage)) {
                        return get_media($val, $storage);
                    }
                }
            }

            return static_asset('images/default/' . $size . '.png');
        }
    }

    if (!function_exists('generateBangladeshPhoneNumber')) {
        function generateBangladeshPhoneNumber()
        {
            $prefixes = ['017', '018', '019', '015', '016'];

            $randomPrefix = $prefixes[array_rand($prefixes)];
            $randomNumber = Str::random(8, '0123456789');

            return $randomPrefix . $randomNumber;
        }
    }

    if (!function_exists('sendMail')) {

        /**
         * description
         *
         * @param
         * @return
         */
        function sendMail($user, $code, $purpose, $password = '')
        {

            if ($purpose == 'verify_email'):
                $url = url('/') . '/activation/' . $user->email . '/' . $code;
                $view = 'merchant.auth.mail.activate-account-email';
                $subject = __('verify_email_subject');
            elseif ($purpose == 'forgot_password'):
                $url = url('/') . '/reset/' . $user->email . '/' . $code;
                $view = 'admin.auth.mail.forgot-password-email';
                $subject = __('reset_password_subject');
            elseif ($purpose == 'verify_email_success'):
                $url = '';
                $view = 'merchant.auth.mail.registration-success-email';
                $subject = __('verify_email_success_subject');
            else:
                $url = '';
                $view = 'admin.auth.mail.reset-success-email';
                $subject = __('reset_password_success_subject');
            endif;

            $data = ['url' => $url, 'user' => $user];


            Mail::send($view, [
                'data' => $data
            ], function ($message) use ($user, $subject) {
                $message->to([$user->email]);
                $message->replyTo('info@delix.com.bd', __('app_name'));
                $message->subject($subject);
            });
        }
    }

    if (!function_exists('sendMailTo')) {

        function sendMailTo($email, $data)
        {
            Mail::send('setting::email.email_template', [
                'templateBody' => $data->message
            ], function ($message) use ($email, $data) {
                $message->to($email);
                $message->replyTo('info@delix.com.bd', __('app_name'));
                $message->subject($data->subject);
            });
        }
    }


    if (!function_exists('google_fonts_list')) {
        function google_fonts_list()
        {
            $path = storage_path() . '/json/fonts.json';

            return json_decode(file_get_contents($path), true);
        }
    }

    if (!function_exists('css_font_name')) {
        function css_font_name($name)
        {
            $name = trim($name, '');
            $name = ucwords($name, '_');

            return str_replace('_', ' ', $name);
        }
    }

    if (!function_exists('font_link')) {
        function font_link()
        {
            $url = '<link rel="preconnect" href="https://fonts.googleapis.com">';
            $url .= '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';

            // header font
            $header_font_name = setting('header_font', app()->getLocale());
            $header_font_name = trim($header_font_name, '');
            $header_font_name = ucwords($header_font_name, '_');
            $header_font_name = str_replace('_', '+', $header_font_name);
            $url .= '<link href="https://fonts.googleapis.com/css2?family=' . $header_font_name . ':wght@400;500;600;700&display=swap" rel="stylesheet">';

            if (setting('body_font') == setting('header_font')) {
                return $url;
            }

            //body font
            $body_font_name = setting("body_font", app()->getLocale());
            if ($header_font_name != $body_font_name) {
                $body_font_name = trim($body_font_name, '');
                $body_font_name = ucwords($body_font_name, '_');
                $body_font_name = str_replace('_', '+', $body_font_name);
                $url .= '<link href="https://fonts.googleapis.com/css2?family=' . $body_font_name . ':wght@400;500;600;700&display=swap" rel="stylesheet">';
            }

            // header font
            $admin_panel_header_font_name = setting('admin_panel_header_font');
            $admin_panel_header_font_name = trim($admin_panel_header_font_name, '');
            $admin_panel_header_font_name = ucwords($admin_panel_header_font_name, '_');
            $admin_panel_header_font_name = str_replace('_', '+', $admin_panel_header_font_name);
            $url .= '<link href="https://fonts.googleapis.com/css2?family=' . $admin_panel_header_font_name . ':wght@400;500;600;700&display=swap" rel="stylesheet">';

            if (setting('admin_panel_header_font') == setting('admin_panel_body_font')) {
                return $url;
            }

            //body font
            $admin_panel_body_font_name = setting('admin_panel_body_font');
            if ($admin_panel_header_font_name != $admin_panel_body_font_name) {
                $admin_panel_body_font_name = trim($admin_panel_body_font_name, '');
                $admin_panel_body_font_name = ucwords($admin_panel_body_font_name, '_');
                $admin_panel_body_font_name = str_replace('_', '+', $admin_panel_body_font_name);
                $url .= '<link href="https://fonts.googleapis.com/css2?family=' . $admin_panel_body_font_name . ':wght@400;500;600;700&display=swap" rel="stylesheet">';
            }

            return $url;
        }
    }


    if (!function_exists('languageCheck')) {
        function languageCheck()
        {
            if (cache()->has('locale')) {
                $lang = cache()->get('locale');
            } elseif (setting('default_language')) {
                $lang = setting('default_language');
            } else {
                $lang = 'en';
            }

            return $lang;
        }
    }


    if (!function_exists('localeRoutePrefix')) {
        function localeRoutePrefix()
        {
            $current_locale = false;
            $current_url = url()->current();
            $locale = languageCheck();
            $current_url_explodes = explode('/', $current_url);
            $all_locales = app('languages')->pluck('locale')->toArray();
            foreach ($all_locales as $all_locale) {
                if (in_array($all_locale, $current_url_explodes)) {
                    $locale = $all_locale;
                    $current_locale = true;
                    break;
                }
            }
            if (!$current_locale) {
                $locale = setting('default_language');
            }
            cache()->put('locale', $locale);
            app()->setLocale($locale);
            if ($locale == setting('default_language')) {
                app()->setLocale($locale);
                cache()->put('locale', $locale);
                return '';
            }
            return $locale;
        }
    }
    if (!function_exists('setLanguageRedirect')) {
        function setLanguageRedirect($language_locale): array|string
        {
            $current_url = \request()->fullUrl();
            $locale = languageCheck();
            $current_locale = '';
            $current_url_explodes = explode('/', $current_url);
            $all_locales = app('languages')->pluck('locale')->toArray();
            foreach ($all_locales as $all_locale) {
                if (in_array($all_locale, $current_url_explodes)) {
                    $current_locale = $all_locale;
                    break;
                }
            }
            if ($current_locale) {
                $reload_url = str_replace("/$locale", "/$language_locale", $current_url);
            } else {
                $reload_url = str_replace(url(''), url("/$language_locale"), $current_url);
            }
            if ($language_locale == setting('default_language')) {
                $reload_url = str_replace("/$language_locale", '', $reload_url);
            }
            return $reload_url;
        }
    }

    if (!function_exists('systemLanguage')) {
        function systemLanguage()
        {
            $languages = app('languages');
            return $languages->where('locale', app()->getLocale())->first();
        }
    }


    if (!function_exists('menuActivation')) {
        function menuActivation($urls, $class, $other = null)
        {
            $check_lang = app()->getLocale() == setting('default_language') ? '' : app()->getLocale() . '/';

            if (is_array($urls)) {
                foreach ($urls as $url) {
                    if (Request::is($check_lang . $url)) {
                        return $class;
                    }
                }
            } elseif (Request()->is($check_lang . $urls)) {
                return $class;
            } else {
                return $other;
            }
        }
    }

    if (!function_exists('getArrayValue')) {
        function getArrayValue($key, $array, $default = null)
        {
            return arrayCheck($key, $array) ? $array[$key] : $default;
        }
    }
    if (!function_exists('getInputValue')) {
        function getInputValue($value)
        {
            return $value ?: old($value);
        }
    }
    if (!function_exists('get_yrsetting')) {

        function get_yrsetting($setting_for)
        {
            return config()->get('lmssetting.' . $setting_for);
        }
    }

    if (!function_exists('validate_purchase')) {
        function validate_purchase($code, $data)
        {
            $script_url = str_replace('install/process', '', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

            $fields = [
                'item_id' => '55375587',
                'activation_code' => urlencode($code),
                'current_version' => setting('current_version'),
            ];
            $response = false;
            if (config('app.beta_channel')) {
                $url = 'https://license.spagreen.net/version-check-including-beta';
            } else {
                $url = 'https://license.spagreen.net/version-check';
            }

            $request = curlRequest($url, $fields);
            if (property_exists($request, 'status') && $request->status) {
                $response = $request->release_info;
            }
            $install_version = 220;
            $fields = [
                'domain' => urlencode($_SERVER['SERVER_NAME']),
                'version' => $install_version,
                'item_id' => '55375587',
                'url' => urlencode($script_url),
                'activation_code' => urlencode($code),
                'is_beta' => (config('app.beta_channel')) ? '1' : '0',
            ];

            $curl_response = curlRequest('https://license.spagreen.net/verify-installation-v3', $fields);

            if (property_exists($curl_response, 'status') && $curl_response->status) {
                envWrite('DB_HOST', $data['DB_HOST']);
                envWrite('DB_DATABASE', $data['DB_DATABASE']);
                envWrite('DB_USERNAME', $data['DB_USERNAME']);
                envWrite('DB_PASSWORD', $data['DB_PASSWORD']);
                sleep(5);

                $zip_file = $curl_response->release_zip_link;

                if ($zip_file) {
                    try {
                        $file_path = base_path('public/install/installer.zip');
                        file_put_contents($file_path, file_get_contents($zip_file));
                    } catch (Exception $e) {
                        return 'Zip file cannot be Imported. Please check your server permission or Contact with Script Author.';
                    }
                }

                return 'success';
            } else {
                return $curl_response->message;
            }
        }
    }

    if (!function_exists('isRtl')) {

        function isRtl() {
            $rtl_langs = ['ar', 'he', 'ur', 'arc', 'az', 'dv', 'ku', 'fa'];
            if(in_array(App::getLocale(), $rtl_langs)) {
                return 'rtl';
            } else {
                return 'ltr';
            }
        }
    }

