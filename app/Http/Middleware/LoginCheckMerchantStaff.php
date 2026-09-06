<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LoginCheckMerchantStaff
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
            if(Sentinel::getUser()->user_type == 'merchant_staff'):
                return $next($request);
            elseif(Sentinel::getUser()->user_type == 'merchant'):
                return redirect()->route('merchant.dashboard');
            endif;
            return redirect()->route('dashboard');

        endif;
        return redirect()->route('login');

    }
}
