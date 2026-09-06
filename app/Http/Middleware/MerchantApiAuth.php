<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MerchantApiAuth
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
        if($request->header('Api-Key') != '' && $request->header('Secret-Key') != ''):
            return $next($request);
        endif;

        $response = [
            'status' => 401,
            'message' => __('provide_required_header'),
        ];

        return response()->json($response, 401);
    }
}
