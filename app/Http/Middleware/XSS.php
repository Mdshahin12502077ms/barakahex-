<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XSS
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
// dd(\Request::ip());
//         if(preg_match('/^162.158.{1,3}[0-9].{1,3}[0-9]/',\Request::ip())):
//             return abort(403);
//         endif;

       /*  $userInput = $request->all();
        array_walk_recursive($userInput, function (&$userInput) {
        $userInput = strip_tags($userInput);
        });
        $request->merge($userInput); */
        return $next($request);
    }
}
