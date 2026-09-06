<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LogoutCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Sentinel::check()) :
            return $next($request);
        endif;

        // Allow logout requests to proceed
        if ($request->is('*logout*') || $request->routeIs('logout') || $request->routeIs('*.logout')) {
            return $next($request);
        }

        $userType = Sentinel::getUser()->user_type;
        if($userType == 'merchant'):
            return redirect()->route('merchant.dashboard');
        elseif($userType == 'merchant_staff'):
            return redirect()->route('merchant.staff.dashboard');
        elseif($userType == 'delivery' || $userType == 'delivery_man'):
            return redirect()->route('deliveryman.dashboard');
        endif;
        return redirect()->route('dashboard');
    }
}
