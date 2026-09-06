<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LoginCheck
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
            if ($userType == 'staff' || $userType == 'admin' || $userType == 'super_admin'):
                return $next($request);
            elseif ($userType == 'delivery' || $userType == 'delivery_man'):
                return redirect()->route('deliveryman.dashboard');
            elseif ($userType == 'merchant_staff'):
                return redirect()->route('merchant.staff.dashboard');
            endif;
            return redirect()->route('merchant.dashboard');
        endif;
        return redirect()->route('login');
    }
}
