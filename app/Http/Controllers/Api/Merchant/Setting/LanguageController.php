<?php

namespace App\Http\Controllers\Api\Merchant\Setting;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Traits\SendMailTrait;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class LanguageController extends Controller
{
    use ApiReturnFormatTrait;

    public function language(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user               = jwtUser();
            $languageFilePath   = base_path('lang');

            if ($request->bn) {
                $language       = file_get_contents($languageFilePath . '/bn.json');
            } else {
                $language       = file_get_contents($languageFilePath . '/en.json');
            }

            $languageArray      = json_decode($language, true);

            $data = [
                'language' => $languageArray,
            ];

            return $this->responseWithSuccess('language_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

}
