<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LoginCheckDeliveryMan
{
    public function handle(Request $request, Closure $next)
    {
        if (Sentinel::check() && in_array(Sentinel::getUser()->user_type, ['delivery_man', 'delivery'])) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
