<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LoginCheckMerchant
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
        if (Sentinel::check()) :
            $userType = Sentinel::getUser()->user_type;
            if($userType == 'merchant'):
                return $next($request);
            elseif($userType == 'merchant_staff'):
                return redirect()->route('merchant.staff.dashboard');
            elseif($userType == 'delivery' || $userType == 'delivery_man'):
                return redirect()->route('deliveryman.dashboard');
            elseif(in_array($userType, ['staff', 'admin', 'super_admin'])):
                return redirect()->route('dashboard');
            endif;
        endif;
        return redirect()->route('login');
    }
}
