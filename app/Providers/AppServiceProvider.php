<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Addon;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Setting;
use App\Models\Branch;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Sentinel;
use App\Repositories\Admin\DistrictRepository;
use App\Repositories\Interfaces\Admin\DistrictRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DistrictRepositoryInterface::class, DistrictRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);
        $this->app->singleton('settings', function () {
            return Cache::rememberForever('settings', function () {
                return Schema::hasTable('settings') ? Setting::all() : collect();
            });
        });
        $this->app->singleton('languages', function () {
            return Cache::rememberForever('languages', function () {
                return Schema::hasTable('languages') ? Language::where('status', 1)->get() : collect();
            });
        });
        $this->app->singleton('currencies', function () {
            return Cache::rememberForever('currencies', function () {
                return Schema::hasTable('currencies') ? Currency::where('status', 1)->get() : collect();
            });
        });

        view::composer(['backend.layouts.package_subscribe'], function ($view) {
            $notifications = Notification::select('notifications.*', 'nu.id as notification_user_id')
                ->join('notification_users as nu', 'nu.notification_id', '=', 'notifications.id')
                ->where('nu.user_id', \Sentinel::getUser()->id)->where('nu.is_read', 0)
                ->groupBy('nu.notification_id')
                ->latest()->limit(5)->get();
            $view->with([
                'notifications' => $notifications,
            ]);
        });
        view::composer(['backend.layouts.header'], function ($view) {
            $notificationCount = NotificationUser::where('user_id', Sentinel::getUser()->id)->where('is_read', 0)->count();
            $view->with([
                'notificationCount' => $notificationCount
            ]);
        });

        view::composer(['merchant.profile.modals'], function ($view) {
            $branchs = Branch::active()->get();
            $view->with([
                'branchs' => $branchs,
            ]);
        });
    }
}
