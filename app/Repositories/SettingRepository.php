<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingRepository
{
    use ImageTrait;

    public function update($request): bool
    {
        
        DB::beginTransaction();
        try{
        $site_lang = $request->site_lang ?? 'en';


        if (!empty($request->input('social_media'))) {
            $socialArray = [];
            foreach ($request->input('social_media') as $_key => $socialMedia) {
                $url = $request->social_media_url[$_key];
                $socialArray[$socialMedia] = $url;
            }

            $title = 'social_media';
            $value = json_encode($socialArray);


            $existingSetting = Setting::where('title', $title)->first();

            if ($existingSetting) {
                // Update existing setting
                $existingSetting->value = $value;
                $existingSetting->save();
            } else {
                // Create new setting
                $setting = new Setting();
                $setting->title = $title;
                $setting->value = $value;
                $setting->save();
            }
        }

        foreach ($request->except('_token', '_method', 'site_lang', 'mobile_app', 'chat_messenger', 'countries', 'r','social_media','social_media_url') as $key => $value) {

            if ($key !== 'social_media' && $key !== 'social_media_url') {

            if ($key == 'default_language') {
                $setting = Setting::where('title', $key)->first();
            } else {
                if (isset($site_lang) && in_array($key, get_yrsetting('setting_by_lang'))) {
                    $setting = Setting::where('title', $key)->where('lang', $site_lang)->first();
                } else {
                    $setting = Setting::where('title', $key)->where('lang', 'en')->first();
                }
            }


            if (in_array($key, get_yrsetting('setting_image'))) {

                if (!blank($setting)) {
                    $this->deleteImage(setting($key));
                }

                $response = $this->saveImage($request->file($key), $key);


                $value    = serialize($response['images']);

            }

            if (in_array($key, get_yrsetting('setting_array'))) {
                $value = serialize($value);
            }
            if (blank($setting)) {
                $setting        = new Setting();
                $setting->title = $key;
                //if change by chosen lang set lang = chosen lang
           // }
           // if (blank($setting)) {
           //     $setting        = new Setting();
             //   $setting->title = $key;
                //if change by chosen lang set lang = chosen lang
                if (isset($site_lang) && in_array($key, get_yrsetting('setting_by_lang'))) {
                    $setting->lang = $site_lang;
                } else {
                    $setting->lang = 'en';
                }
                $setting->value = $value;
            } else {
                //if change by chosen lang set lang = chosen lang
                if (isset($site_lang) && in_array($key, get_yrsetting('setting_by_lang'))) {
                    $setting->lang = $site_lang;
                } else {
                    $setting->lang = 'en';
                }
                $setting->value = $value;
            }


            $setting->save();

        }


        }


        Cache::flush();

        if ($request->has('system_name')) {
            $system_name = Setting::where('title', 'system_name')->where('lang', config('app.locale'))->first();
            if (! blank($system_name)) {
                envWrite('APP_NAME', $system_name->value);
            } else {
                $system_name = Setting::where('title', 'system_name')->first();
                if (! blank($system_name)) {
                    envWrite('APP_NAME', $system_name->value);
                }
            }
        }

        if ($request->has('is_cache_enabled')) {
            if (setting('is_cache_enabled') == 'enable') {
                if (setting('default_cache') == 'redis') {
                    envWrite('CACHE_DRIVER', 'redis');
                    envWrite('REDIS_CLIENT', 'predis');
                    envWrite('REDIS_HOST', setting('redis_host'));
                    envWrite('REDIS_PASSWORD', setting('redis_password'));
                    envWrite('REDIS_PORT', setting('redis_port'));
                } else {
                    envWrite('CACHE_DRIVER', 'file');
                }
            } else {
                envWrite('CACHE_DRIVER', 'file');
            }
        }
        if ($request->has('default_storage')) {
            if ($request->default_storage == 'aws_s3') {
                $aws_url = 'http://'.setting('aws_bucket').'.s3.'.setting('aws_default_region').'.amazonaws.com';

                envWrite('AWS_ACCESS_KEY_ID', setting('aws_access_key_id'));
                envWrite('AWS_SECRET_ACCESS_KEY', setting('aws_secret_access_key'));
                envWrite('AWS_DEFAULT_REGION', setting('aws_default_region'));
                envWrite('AWS_BUCKET', setting('aws_bucket'));
                envWrite('AWS_URL', $aws_url);
                envWrite('FILESYSTEM_DRIVER', 's3');
            } elseif ($request->default_storage == 'wasabi') {
                $was_url = 'https://'.setting('wasabi_bucket').'.s3.'.setting('wasabi_default_region').'.wasabisys.com';

                envWrite('WAS_ACCESS_KEY_ID', setting('wasabi_access_key_id'));
                envWrite('WAS_SECRET_ACCESS_KEY', setting('wasabi_secret_access_key'));
                envWrite('WAS_DEFAULT_REGION', setting('wasabi_default_region'));
                envWrite('WAS_BUCKET', setting('wasabi_bucket'));
                envWrite('WAS_URL', $was_url);
                envWrite('FILESYSTEM_DRIVER', 'wasabi');
            } else {
                envWrite('FILESYSTEM_DRIVER', 'local');
            }
        }

        if ($request->has('pusher_app_key')) {
            //pushar
            if (checkEmptyProvider('is_pusher_notification_active')) {
                envWrite('PUSHER_APP_KEY', setting('pusher_app_key'));
                envWrite('PUSHER_APP_SECRET', setting('pusher_app_secret'));
                envWrite('PUSHER_APP_ID', setting('pusher_app_id'));
                envWrite('PUSHER_APP_CLUSTER', setting('pusher_app_cluster'));
            }
        }

        DB::commit();
        return true;

     } catch (\Exception $e) {
         DB::rollback();
         //dd($e->getMessage());
         return false;
     }
    }

    public function statusChange($request): bool
    {
        if (in_array($request['name'], get_yrsetting('setting_by_lang'))) {
            $default_language = setting('default_language');
        } else {
            $default_language = 'en';
        }
        $setting            = Setting::where('title', $request['name'])->where('lang', $default_language)->first();

        if (! $setting) {
            $setting        = new Setting();
            $setting->title = $request['name'];
        }

        $setting->value     = $request['value'];
        $setting->lang      = $default_language;

        $setting->save();

        Artisan::call('optimize:clear');

        if (in_array('is_pusher_notification_active', $request)) {
            $setting = Setting::where('title', 'is_pusher_notification_active')->where('lang', $default_language)->first();
            if ($setting->value == 1) {
                envWrite('BROADCAST_DRIVER', 'pusher');
            } else {
                envWrite('BROADCAST_DRIVER', 'null');
            }
        }

        return true;
    }
}
