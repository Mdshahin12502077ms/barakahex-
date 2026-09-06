<?php

    namespace App\Http\Middleware;

    use App\Traits\ApiReturnFormatTrait;
    use Closure;
    use App\Models\ApiKey;
    use Illuminate\Http\Request;

    class CheckApiKeyMiddleware
    {
        use ApiReturnFormatTrait;
        /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle(Request $request, Closure $next)
        {

            if ($request->hasHeader('apikey')) {
                $api_check = ApiKey::where('key', $request->header('apikey'))->where('status', 1)->first();
                if ($api_check) {
                    return $next($request);
                } else {
                    return $this->responseWithError('API key invalid');
                }
            } else {
                return $this->responseWithError('API key missing');

            }
        }
    }
