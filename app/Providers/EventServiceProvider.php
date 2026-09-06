<?php

namespace App\Providers;

use App\Models\Parcel;
use App\Models\Account\MerchantWithdraw;
use App\Observers\ParcelObserver;
use App\Observers\MerchantWithdrawObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Parcel::observe(ParcelObserver::class);
        MerchantWithdraw::observe(MerchantWithdrawObserver::class);
    }
}
