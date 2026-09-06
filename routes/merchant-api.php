<?php

    use App\Http\Controllers\Api\Merchant\AuthController;
    use App\Http\Controllers\Api\Merchant\ShopController;
    use App\Http\Controllers\Api\Merchant\StaffController;
    use App\Http\Controllers\Api\Merchant\PayoutController;
    use App\Http\Controllers\Api\Merchant\PayoutLogController;
    use App\Http\Controllers\Api\Merchant\PayoutDetailsController;
    use App\Http\Controllers\Api\Merchant\ParcelController;
    use App\Http\Controllers\Api\Merchant\Setting\MerchantController;
    use App\Http\Controllers\Api\Merchant\Setting\DefaultPayoutController;
    use App\Http\Controllers\Api\Merchant\Setting\BankController;
    use App\Http\Controllers\Api\Merchant\Setting\MFSController;
    use App\Http\Controllers\Api\Merchant\Setting\LanguageController;
    use App\Http\Controllers\Api\Merchant\Setting\NotificationController;
    use App\Http\Controllers\Api\Merchant\ChargeController;
    use App\Http\Controllers\Api\Merchant\ApiController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;


    Route::group(['prefix' => '/merchant'], function () {
        Route::middleware(['merchant.apikey'])->group(function () {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('sign-up',[AuthController::class, 'signUp']);
            Route::post('otp', [AuthController::class, 'otp']);
        });
        Route::middleware(['jwt.verify','merchant.apikey'])->group(function () {
            Route::get('dashboard',[AuthController::class,'dashboard']);
            Route::get('profile',[AuthController::class,'profile']);
            Route::post('update-profile',[AuthController::class, 'updateProfile']);
            Route::post('change-password',[AuthController::class, 'changePassword']);
            Route::get('login-activity',[AuthController::class,'loginActivity']);
            Route::post('logout',[AuthController::class, 'logout']);
            Route::get('shop', [ShopController::class, 'allShop']);
            Route::get('branch', [ShopController::class, 'allBranch']);
            Route::post('shop-store', [ShopController::class, 'submitShop']);
            Route::post('shop-update/{id}', [ShopController::class, 'submitShop']);
            Route::get('staff', [StaffController::class, 'allStaff']);
            Route::post('staff-store', [StaffController::class, 'submitStaff']);
            Route::get('edit-staff/{id}', [StaffController::class, 'editStaff']);
            Route::post('staff-update/{id}', [StaffController::class, 'submitStaff']);
            Route::get('payout', [PayoutController::class, 'allPayout']);
            Route::get('payout-balance', [PayoutController::class, 'payoutBalance']);
            Route::get('payout-account', [PayoutController::class, 'account']);
            Route::post('payout-store', [PayoutController::class, 'submitPayout']);
            Route::get('payout-log', [PayoutLogController::class, 'allPayoutLog']);
            Route::get('payout-details/{id}', [PayoutDetailsController::class, 'payoutDetails']);
            Route::post('parcel', [ParcelController::class, 'allParcel']);
            Route::post('parcel-download', [ParcelController::class, 'parcelDownload']);
            Route::post('parcel-store', [ParcelController::class, 'submitParcel']);
            Route::post('parcel-update/{id}', [ParcelController::class, 'submitParcel']);
            Route::get('parcel-details/{id}', [ParcelController::class, 'parcelDetail']);
            Route::get('parcel-status', [ParcelController::class, 'parcelStatus']);
            Route::post('parcel-delete', [ParcelController::class, 'parcelDelete']);
            Route::post('parcel-cancel',[ParcelController::class,'cancelParcel']);
            Route::post('percel-tracking', [ParcelController::class,'PercelTracking']);
            Route::get('parcel-setting', [ParcelController::class, 'setting']);
            Route::post('import', [ParcelController::class, 'import']);
            Route::get('charge', [ChargeController::class, 'charge']);
            Route::get('api', [ApiController::class, 'api']);

            Route::get('notification', [NotificationController::class, 'notification']);
            Route::get('notification-update/{id}', [NotificationController::class, 'updateNotification']);

            Route::post('language', [LanguageController::class, 'language']);


            //----------setting--------//
            Route::get('merchant', [MerchantController::class, 'merchant']);
            Route::post('merchant-update', [MerchantController::class, 'updateMerchant']);
            Route::get('default-payout', [DefaultPayoutController::class, 'defaultPayout']);
            Route::post('default-payout-update', [DefaultPayoutController::class, 'updateDefaultPayout']);
            Route::get('bank', [BankController::class, 'bank']);
            Route::post('bank-update', [BankController::class, 'updateBank']);
            Route::get('mfs', [MFSController::class, 'mfs']);
            Route::post('mfs-update', [MFSController::class, 'updateMfs']);
        });
    });
