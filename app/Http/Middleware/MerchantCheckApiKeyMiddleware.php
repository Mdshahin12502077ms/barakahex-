<?php

    namespace App\Http\Middleware;

    use App\Models\ApiKey;
    use App\Models\Merchant;
    use App\Traits\ApiReturnFormatTrait;
    use Closure;

    class MerchantCheckApiKeyMiddleware
    {
        use ApiReturnFormatTrait;

        public function handle($request, Closure $next)
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
