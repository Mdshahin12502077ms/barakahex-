<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BagController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\BranchManagerDashboardController;
use App\Http\Controllers\Admin\BulkController;
use App\Http\Controllers\Admin\BulkWithdrawController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CrmHistoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryManController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\Email\EmailController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\FinancialController;
use App\Http\Controllers\Admin\FundTransferController;
use App\Http\Controllers\Admin\HubReportController;
use App\Http\Controllers\Admin\ImportExportController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LiveSearchController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\MobileAppSetting\ApiKeyController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\ParcelController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PercelOtpController;
use App\Http\Controllers\Admin\PreferenceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RiderReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\ThanaUpazilaController;
use App\Http\Controllers\Admin\ThirdPartyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UtilityController;
use App\Http\Controllers\Admin\WebsiteSetting\FooterSettingController;
use App\Http\Controllers\Admin\WebsiteSetting\HeaderSettingController;
use App\Http\Controllers\Admin\WebsiteSetting\WebsiteAboutController;
use App\Http\Controllers\Admin\WebsiteSetting\WebsiteFeatureController;
use App\Http\Controllers\Admin\WebsiteSetting\WebsiteNewsAndEventController;
use App\Http\Controllers\Admin\WebsiteSetting\WebsitePageController;
use App\Http\Controllers\Admin\WebsiteSetting\WebsitePartnerLogoController;
use App\Http\Controllers\Admin\WebsiteSetting\WebsiteServiceController;
use App\Http\Controllers\Admin\WebsiteSetting\WebsiteSettingController;
use App\Http\Controllers\Admin\WebsiteSetting\WebsiteStatisticController;
use App\Http\Controllers\Admin\WebsiteSetting\WebsiteTestimonialController;
use App\Http\Controllers\Admin\WithdrawController as AdminWithdrawController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ContactController as ContactsController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Merchant\DashboardController as MerchantDashboardController;
use App\Http\Controllers\Merchant\MerchantStaffController as MerchantStaffController;
use App\Http\Controllers\Merchant\ParcelController as MerchantParcelController;
use App\Http\Controllers\Merchant\ProductController;
use App\Http\Controllers\Merchant\ProfileController;
use App\Http\Controllers\Merchant\StockController;
use App\Http\Controllers\Merchant\WarehouseController;
use App\Http\Controllers\Merchant\WithdrawController as MerchantWithdrawController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;












Route::get('/cc', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "Cleared!";
});
Route::get('cron-run', [CronController::class, 'cron'])->name('cron.run.manually');

Route::get('/get-shops-by-merchant', [ImportExportController::class, 'getShopsByMerchant'])->name('get.shops.by.merchant');

Route::group(['middleware' => 'isInstalled', 'prefix' => localeRoutePrefix()], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('language/{lang}', [HomeController::class, 'changeLanguage'])->name('lang');
    Route::post('contacts-store', [ContactsController::class, 'contact'])->name('contacts-store');
    Route::post('charges-details', [HomeController::class, 'chargeDetails'])->name('charges-details');
    Route::get('tracking/{tracking_no?}', [TrackingController::class, 'index'])->name('tracking');
    Route::get('tracking-parcel/{parcel_no?}', [TrackingController::class, 'tracking'])->name('tracking.parcel');
    Route::get('page/{link}', [PrivacyPolicyController::class, 'index']);
});
//frontend

Route::group(['middleware' => 'XSS'], function () {
    Route::group(['prefix' => localeRoutePrefix()], function () {
        // before login
        Route::middleware(['LogoutCheck'])->group(function () {
            Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
            Route::post('login', [AuthController::class, 'login'])->name('login');
            Route::get('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
            Route::post('forgot-password', [AuthController::class, 'forgotPasswordPost'])->name('forgot-password');
            Route::get('reset/{email}/{activationCode}', [AuthController::class, 'resetPassword']);
            Route::post('reset/{email}/{activationCode}', [AuthController::class, 'PostResetPassword'])->name('reset-password');
            Route::get('activation/{email}/{activationCode}', [AuthController::class, 'activation']);

            //merchant routes
            Route::get('register', [AuthController::class, 'registerForm'])->name('register');
            Route::post('register', [AuthController::class, 'register'])->name('register');
            Route::get('confirm-otp', [AuthController::class, 'otpConfirm'])->name('confirm-otp');
            Route::post('confirm-otp', [AuthController::class, 'otpConfirmPost'])->name('confirm-otp');
            Route::get('request-otp/{id}', [AuthController::class, 'otpRequest'])->name('request-otp');
        });

        // common route after login
        Route::middleware(['LoginCheckCommon'])->group(function () {
            Route::get('mode-change', [CommonController::class, 'modeChange']);
            Route::get('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('logout-other-devices', [CommonController::class, 'logoutOtherDevices'])->name('logout.other.devices');

            // Notifications
            Route::get('notifications', [NotificationController::class, 'allNotifications'])->name('all.notifications');
        });

        // after login
        Route::middleware(['LoginCheck'])->group(function () {

            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('default', [DashboardController::class, 'report'])->name('admin.default.dashboard');
            Route::get('custom-report', [DashboardController::class, 'customDateRange'])->name('admin.default.dashboard.custom');

            Route::get('earning-report', [DashboardController::class, 'financeReportStats'])->name('earning.report');
            Route::get('parcel-report', [DashboardController::class, 'parcelReportStats'])->name('parcel.report');

            //parcel bulk import
            Route::get('download-sample', [ImportExportController::class, 'export'])->name('export');

            Route::prefix('admin')->group(function () {
                Route::resource('roles', RoleController::class);
                Route::POST('role-status', [RoleController::class, 'statusChange'])->name('admin.role.update-status');
                Route::get('users', [UserController::class, 'index'])->name('users')->middleware('PermissionCheck:user_read');
                Route::get('user/create', [UserController::class, 'create'])->name('user.create')->middleware('PermissionCheck:user_create');
                Route::post('user/store', [UserController::class, 'store'])->name('user.store')->middleware('PermissionCheck:user_create');
                Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('user.edit')->middleware('PermissionCheck:user_update');
                Route::post('user/update', [UserController::class, 'update'])->name('user.update')->middleware('PermissionCheck:user_update');
                Route::delete('user/delete/{id}', [UserController::class, 'delete'])->middleware('PermissionCheck:user_delete');

                Route::get('change-role', [UserController::class, 'changeRole']);
                Route::POST('user-status', [UserController::class, 'statusChange'])->name('admin.user.update-status');
                // staff detail
                Route::get('staff-personal-info/{id}', [UserController::class, 'personalInfo'])->name('detail.staff.personal.info');
                Route::get('staff-account-activity/{id}', [UserController::class, 'accountActivity'])->name('detail.staff.account-activity');
                Route::get('staff-transaction-log/{id}', [UserController::class, 'paymentLogs'])->name('detail.staff.payment.logs');

                Route::get('staff/profile', [CommonController::class, 'profile'])->name('staff.profile');
                Route::get('staff/transaction-log', [CommonController::class, 'paymentLogs'])->name('staff.payment.logs');
                Route::get('staff/notifications', [CommonController::class, 'notification'])->name('staff.notifications');
                Route::get('staff/account-activity', [CommonController::class, 'accountActivity'])->name('staff.account-activity');
                Route::get('staff/security-settings', [CommonController::class, 'securitySetting'])->name('staff.security-settings');
                Route::post('staff/change-password', [CommonController::class, 'changePassword'])->name('staff.change-password');
                Route::post('staff/profile-update', [CommonController::class, 'profileUpdate'])->name('staff.update.profile');


                //notification
                Route::get('notification/update/{id}', [NotificationController::class, 'index'])->name('notification.update');

                //merchant
                Route::get('merchants', [MerchantController::class, 'index'])->name('merchant')->middleware('PermissionCheck:merchant_read');
                Route::get('merchant/create', [MerchantController::class, 'create'])->name('merchant.create')->middleware('PermissionCheck:merchant_create');
                Route::post('merchant/store', [MerchantController::class, 'store'])->name('merchant.store')->middleware('PermissionCheck:merchant_create');
                Route::get('merchant/edit/{id}', [MerchantController::class, 'edit'])->name('merchant.edit')->middleware('PermissionCheck:merchant_update');
                Route::post('merchant/update', [MerchantController::class, 'update'])->name('merchant.update')->middleware('PermissionCheck:merchant_update');
                Route::delete('merchant/delete/{id}', [MerchantController::class, 'delete'])->middleware('PermissionCheck:merchant_delete');
                Route::any('merchant-filter', [MerchantController::class, 'filter'])->name('merchant.filter')->middleware('PermissionCheck:merchant_read');
                Route::POST('merchant/update-status', [MerchantController::class, 'statusChange'])->name('admin.merchant.status')->middleware('PermissionCheck:merchant_update');
                Route::get('merchant/personal-info/{id}', [MerchantController::class, 'personalInfo'])->name('detail.merchant.personal.info')->middleware('PermissionCheck:merchant_read');
                Route::get('merchant/account-activity/{id}', [MerchantController::class, 'accountActivity'])->name('detail.merchant.account-activity')->middleware('PermissionCheck:merchant_account_activity_read');
                Route::get('merchant/permissions/{id}', [MerchantController::class, 'permissions'])->name('detail.merchant.permissions')->middleware('PermissionCheck:merchant_read');
                Route::post('merchant/permission-update/{id}', [MerchantController::class, 'permissionUpdate'])->name('detail.merchant.permission.update')->middleware('PermissionCheck:merchant_staff_update');
                Route::get('merchant/charge/{id}', [MerchantController::class, 'charge'])->name('detail.merchant.charge')->middleware('PermissionCheck:merchant_charge_read');
                Route::get('merchant/cod-charge/{id}', [MerchantController::class, 'codCharge'])->name('detail.merchant.cod.charge')->middleware('PermissionCheck:merchant_cod_charge_read');
                Route::get('merchant/company/{id}', [MerchantController::class, 'company'])->name('detail.merchant.company')->middleware('PermissionCheck:merchant_read');
                Route::get('merchant/statements/{id}', [MerchantController::class, 'statements'])->name('detail.merchant.statements')->middleware('PermissionCheck:merchant_payment_logs_read');
                Route::get('merchant/shops/{id}', [MerchantController::class, 'shops'])->name('detail.merchant.shops')->middleware('PermissionCheck:merchant_shop_read');

                Route::get('merchant/staffs/{id}', [StaffController::class, 'staffs'])->name('detail.merchant.staffs')->middleware('PermissionCheck:merchant_staff_read');
                Route::get('merchant-staff/create/{id}', [StaffController::class, 'staffCreate'])->name('detail.merchant.staff.create')->middleware('PermissionCheck:merchant_staff_create');
                Route::post('merchant-staff/store', [StaffController::class, 'staffStore'])->name('detail.merchant.staff.store')->middleware('PermissionCheck:merchant_staff_create');
                Route::get('merchant-staff/edit/{id}', [StaffController::class, 'staffEdit'])->name('detail.merchant.staff.edit')->middleware('PermissionCheck:merchant_staff_update');
                Route::post('merchant-staff/update', [StaffController::class, 'staffUpdate'])->name('detail.merchant.staff.update')->middleware('PermissionCheck:merchant_staff_update');
                Route::get('merchant-staff/personal-info/{id}', [StaffController::class, 'personalInfo'])->name('detail.merchant.staff.personal.info')->middleware('PermissionCheck:merchant_staff_read');
                Route::get('merchant/staff-account-activity/{id}', [StaffController::class, 'accountActivity'])->name('detail.merchant.staffs.account-activity')->middleware('PermissionCheck:merchant_staff_read');
                Route::post('merchant/staff/update-status', [StaffController::class, 'statusChange'])->name('admin.merchant-staff.update-status')->middleware('PermissionCheck:merchant_staff_read');

                Route::post('merchant/shop/store', [MerchantController::class, 'shopStore'])->name('admin.merchant.add.shop')->middleware('PermissionCheck:merchant_shop_create');
                Route::delete('merchant/shop/delete/{id}', [MerchantController::class, 'shopDelete'])->middleware('PermissionCheck:merchant_shop_delete');
                Route::get('merchant-api-credentials/{id}', [MerchantController::class, 'apiCredentials'])->name('detail.merchant.api.credentials')->middleware('PermissionCheck:merchant_api_credentials_read');
                Route::post('merchant-api-credentials-update', [MerchantController::class, 'apiCredentialsUpdate'])->name('detail.merchant.api.credentials.update')->middleware('PermissionCheck:merchant_api_credentials_update');
                //shop default status change
                Route::post('merchant/shop-default-update', [MerchantController::class, 'changeDefault'])->name('admin.merchant.default.shop')->middleware('PermissionCheck:merchant_shop_update');
                Route::get('merchant/shop/edit', [MerchantController::class, 'shopEdit'])->name('admin.merchant.edit.shop')->middleware('PermissionCheck:merchant_shop_update');
                Route::post('merchant/shop/update', [MerchantController::class, 'shopUpdate'])->name('admin.merchant.update.shop')->middleware('PermissionCheck:merchant_shop_update');

                Route::get('merchant/payment-accounts/{id}', [MerchantController::class, 'paymentAccounts'])->name('detail.merchant.payment.accounts')->middleware('PermissionCheck:merchant_payment_account_read');

                Route::get('merchant/payment-account-others/{id}', [MerchantController::class, 'paymentOthersAccount'])->name('detail.merchant.payment.accounts.others')->middleware('PermissionCheck:merchant_payment_account_read');
                Route::get('merchant/payment-account-edit/{id}', [MerchantController::class, 'paymentAccountEdit'])->name('detail.merchant.payment.bank.edit')->middleware('PermissionCheck:merchant_payment_account_update');
                Route::post('merchant/payment-account-update', [MerchantController::class, 'paymentAccountUpdate'])->name('detail.merchant.payment.bank.update')->middleware('PermissionCheck:merchant_payment_account_update');
                Route::get('merchant/payment-account-others-edit/{id}', [MerchantController::class, 'paymentAccountOthersEdit'])->name('detail.merchant.payment.others.edit')->middleware('PermissionCheck:merchant_payment_account_update');
                Route::post('merchant/payment-account-others-update', [MerchantController::class, 'paymentAccountOthersUpdate'])->name('detail.merchant.payment.others.update')->middleware('PermissionCheck:merchant_payment_account_update');

                //delivery man
                Route::get('delivery-man', [DeliveryManController::class, 'index'])->name('delivery.man')->middleware('PermissionCheck:deliveryman_read');
                Route::get('delivery-man/create', [DeliveryManController::class, 'create'])->name('delivery.man.create')->middleware('PermissionCheck:deliveryman_create');
                Route::post('delivery-man/store', [DeliveryManController::class, 'store'])->name('delivery.man.store')->middleware('PermissionCheck:deliveryman_create');
                Route::get('delivery-man/edit/{id}', [DeliveryManController::class, 'edit'])->name('delivery.man.edit')->middleware('PermissionCheck:deliveryman_update');
                Route::post('delivery-man/update', [DeliveryManController::class, 'update'])->name('delivery.man.update')->middleware('PermissionCheck:deliveryman_update');
                Route::delete('delivery-man/delete/{id}', [DeliveryManController::class, 'delete'])->middleware('PermissionCheck:deliveryman_delete');
                Route::POST('delivery-man/update-status', [DeliveryManController::class, 'statusChange'])->name('admin.delivery-man.update-status')->middleware('PermissionCheck:deliveryman_update');
                Route::any('delivery-man/filter', [DeliveryManController::class, 'filter'])->name('delivery.man.filter')->middleware('PermissionCheck:deliveryman_read');
                //details
                Route::get('delivery-man/personal-info/{id}', [DeliveryManController::class, 'personalInfo'])->name('detail.delivery.man.personal.info')->middleware('PermissionCheck:deliveryman_read');
                Route::get('delivery-man/account-activity/{id}', [DeliveryManController::class, 'accountActivity'])->name('detail.delivery.man.account-activity')->middleware('PermissionCheck:deliveryman_account_activity_read');
                Route::get('delivery-man/statements/{id}', [DeliveryManController::class, 'statements'])->name('detail.delivery.man.statements')->middleware('PermissionCheck:deliveryman_payment_logs_read');

                Route::get('get-delivery-man-balance/{id}', [DeliveryManController::class, 'balance'])->name('income.delivery.man.balance')->middleware('PermissionCheck:income_create');

                //merchant parcel request
                Route::get('parcel', [ParcelController::class, 'index'])->name('parcel')->middleware('PermissionCheck:parcel_read');
                Route::get('parcel/create', [ParcelController::class, 'create'])->name('parcel.create')->middleware('PermissionCheck:parcel_create');
                Route::post('parcel/store', [ParcelController::class, 'store'])->name('parcel.store')->middleware('PermissionCheck:parcel_create');
                Route::get('parcelbookthana', [ParcelController::class, 'thanabook'])->name('admin.parcel.thanabook');
                Route::get('parcel/edit/{id}', [ParcelController::class, 'edit'])->name('parcel.edit')->middleware('PermissionCheck:parcel_update');
                Route::post('parcel/update', [ParcelController::class, 'update'])->name('parcel.update')->middleware('PermissionCheck:parcel_update');
                Route::post('parcel/delete', [ParcelController::class, 'parcelDelete'])->name('parcel-delete')->middleware('PermissionCheck:parcel_delete');
                Route::any('parcel/filter', [ParcelController::class, 'filter'])->name('admin.parcel.filter');
                Route::get('parcel/detail/{id}', [ParcelController::class, 'detail'])->name('admin.parcel.detail');
                Route::get('parcel/print/{id}', [ParcelController::class, 'print'])->name('admin.parcel.print');
                Route::get('parcel/duplicate/{id}', [ParcelController::class, 'duplicate'])->name('admin.parcel.duplicate');
                Route::get('parcel/sticker/{id}', [ParcelController::class, 'sticker'])->name('admin.parcel.sticker');
                Route::get('parcel/notify/pickup-man/{id}', [ParcelController::class, 'notifyPickupMan'])->name('admin.parcel.notify.pickupman');
                Route::post('parcel/download', [ParcelController::class, 'getParcelDownload'])->name('admin.parcel.download')->middleware('PermissionCheck:parcel_read');

                Route::get('parcel/import', [ImportExportController::class, 'importExportView'])->name('import.csv')->middleware('PermissionCheck:parcel_create');
                Route::post('parcel/import', [ImportExportController::class, 'import'])->name('import')->middleware('PermissionCheck:parcel_create');
                Route::post('parcel/import-preview', [ImportExportController::class, 'preview'])->name('import.preview')->middleware('PermissionCheck:parcel_create');
                Route::post('parcel/import-confirm', [ImportExportController::class, 'confirm'])->name('import.confirm')->middleware('PermissionCheck:parcel_create');
                //bulk work routes
                Route::get('parcel/assigning-delivery-man', [BulkController::class, 'create'])->name('bulk.assigning')->middleware('PermissionCheck:parcel_delivery_assigned');
                Route::get('add-parcel-row/{parcel_no}', [BulkController::class, 'add'])->name('bulk.assigning.parcel');
                Route::post('parcel/assign-delivery-save', [BulkController::class, 'save'])->name('bulk.assigning.parcel.save');
                //for getting shops of selected merchant on select shop dropdown
                Route::get('shops', [ParcelController::class, 'shops'])->name('merchant.change');
                //for getting shop phone number and address
                Route::get('shop', [ParcelController::class, 'shop'])->name('admin.merchant.shop');

                Route::get('product', [ParcelController::class, 'warehousesProduct'])->name('merchant.warehouse.product');

                Route::get('shop/default', [ParcelController::class, 'default']);
                Route::get('merchant/staff', [ParcelController::class, 'merchantStaff']);

                Route::post('assign-pickup-man', [ParcelController::class, 'assignPickupMan'])->name('assign.pickup.man')->middleware('PermissionCheck:parcel_pickup_assigned');
                Route::post('assign-delivery-man', [ParcelController::class, 'assignDeliveryMan'])->name('assign.delivery.man')->middleware('PermissionCheck:parcel_delivery_assigned');
                //re schedule pickup
                Route::post('re-schedule-pickup', [ParcelController::class, 'reSchedulePickup'])->middleware('PermissionCheck:parcel_reschedule_pickup');
                Route::post('re-schedule-pickup-man', [ParcelController::class, 'reSchedulePickupMan'])->name('re-schedule.pickup')->middleware('PermissionCheck:parcel_reschedule_pickup');
                //re schedule delivery
                Route::post('re-schedule-delivery', [ParcelController::class, 'reScheduleDelivery'])->middleware('PermissionCheck:parcel_delivery_assigned');
                Route::post('re-schedule-delivery-man', [ParcelController::class, 'reScheduleDeliveryMan'])->name('re-schedule.delivery')->middleware('PermissionCheck:parcel_reschedule_delivery');
                Route::post('return-assign-to-merchant', [ParcelController::class, 'returnAssignToMerchant'])->name('return.assign.to.merchant')->middleware('PermissionCheck:parcel_return_assigned_to_merchant');
                //cancel parcel with note
                Route::post('parcel-cancel', [ParcelController::class, 'parcelCancel'])->name('parcel-cancel')->middleware('PermissionCheck:parcel_cancel');
                //notes added
                Route::post('parcel-receive-by-pickupman', [ParcelController::class, 'parcelReceiveByPickupman'])->name('parcel-receive-by-pickupman')->middleware('PermissionCheck:parcel_received');

                Route::post('parcel-receive', [ParcelController::class, 'parcelReceive'])->name('parcel-receive')->middleware('PermissionCheck:parcel_received');
                Route::post('parcel-delivered', [ParcelController::class, 'parcelDelivery'])->name('parcel-delivered')->middleware('PermissionCheck:parcel_delivered');
                Route::post('partially-delivered', [ParcelController::class, 'partialDelivery'])->name('partially-delivered')->middleware('PermissionCheck:parcel_delivered');
                Route::post('parcel-returned-to-warehouse', [ParcelController::class, 'parcelReturnToGreenx'])->name('parcel-returned-to-warehouse')->middleware('PermissionCheck:parcel_returned_to_warehouse');
                Route::post('parcel-returned-to-merchant', [ParcelController::class, 'returnToMerchant'])->name('parcel-returned-to-merchant')->middleware('PermissionCheck:parcel_returned_to_merchant');
                Route::post('reverse-from-cancel', [ParcelController::class, 'reverseFromCancel'])->name('reverse-from-cancel')->middleware('PermissionCheck:parcel_backward');
                Route::post('transfer-to-branch', [ParcelController::class, 'transferToBranch'])->name('transfer-to-branch')->middleware('PermissionCheck:parcel_transfer_to_branch');
                Route::post('transfer-receive-to-branch', [ParcelController::class, 'transferReceiveToBranch'])->name('transfer-receive-to-branch')->middleware('PermissionCheck:parcel_transfer_receive_to_branch');
                //
                Route::post('delivery-reverse', [ParcelController::class, 'deliveryReverse'])->name('delivery-reverse')->middleware('PermissionCheck:parcel_backward');

                Route::get('incomes', [AccountController::class, 'index'])->name('incomes')->middleware('PermissionCheck:income_read');
                Route::get('income/create', [AccountController::class, 'create'])->name('incomes.create')->middleware('PermissionCheck:income_create');
                Route::post('income/store', [AccountController::class, 'store'])->name('incomes.store')->middleware('PermissionCheck:income_create');
                Route::get('income/edit/{id}', [AccountController::class, 'edit'])->name('incomes.edit')->middleware('PermissionCheck:income_update');
                Route::post('income/update', [AccountController::class, 'update'])->name('incomes.update')->middleware('PermissionCheck:income_update');
                Route::delete('income/delete/{id}', [AccountController::class, 'delete'])->middleware('PermissionCheck:income_delete');
                Route::get('income/get-delivery-man-balance', [AccountController::class, 'balance'])->name('income.delivery.man.balance')->middleware('PermissionCheck:income_create');

                Route::get('credit-from-merchant-create', [AccountController::class, 'creditCreate'])->name('incomes.receive.from.merchant')->middleware('PermissionCheck:income_create');
                Route::post('credit-from-merchant-store', [AccountController::class, 'creditStore'])->name('incomes.receive.from.merchant.store')->middleware('PermissionCheck:income_create');
                Route::get('credit-from-merchant-edit/{id}', [AccountController::class, 'creditEdit'])->name('incomes.receive.from.merchant.edit')->middleware('PermissionCheck:income_update');
                Route::post('credit-from-merchant-update', [AccountController::class, 'creditUpdate'])->name('incomes.receive.from.merchant.update')->middleware('PermissionCheck:income_update');
                Route::delete('credit-from-merchant-delete/{id}', [AccountController::class, 'creditDelete'])->middleware('PermissionCheck:income_delete');

                Route::get('merchant-parcel', [AccountController::class, 'merchantParcels'])->name('admin.merchant.parcel')->middleware('PermissionCheck:merchant_read');

                Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses')->middleware('PermissionCheck:expense_read');
                Route::get('expense/create', [ExpenseController::class, 'create'])->name('expenses.create')->middleware('PermissionCheck:expense_create');
                Route::post('expense/store', [ExpenseController::class, 'store'])->name('expenses.store')->middleware('PermissionCheck:expense_create');
                Route::get('expense/edit/{id}', [ExpenseController::class, 'edit'])->name('expenses.edit')->middleware('PermissionCheck:expense_update');
                Route::post('expense/update', [ExpenseController::class, 'update'])->name('expenses.update')->middleware('PermissionCheck:expense_update');
                Route::delete('expense/delete/{id}', [ExpenseController::class, 'delete'])->middleware('PermissionCheck:expense_delete');

                Route::get('get-balance-info', [CommonController::class, 'getBalanceInfo']);
                Route::get('clear-cache', [CommonController::class, 'cacheClear'])->name('clear.cache');
                Route::get('get-accounts', [CommonController::class, 'getAccounts']);
                Route::get('user-accounts', [CommonController::class, 'userAccounts'])->name('user.accounts');
                Route::get('staff-accounts/{id}', [UserController::class, 'staffAccounts'])->name('staff.accounts');

                Route::get('accounts', [BankAccountController::class, 'index'])->name('admin.account')->middleware('PermissionCheck:account_read');
                Route::get('account/create', [BankAccountController::class, 'create'])->name('admin.account.create')->middleware('PermissionCheck:account_create');
                Route::post('account/store', [BankAccountController::class, 'store'])->name('admin.account.store')->middleware('PermissionCheck:account_create');
                Route::get('account/edit/{id}', [BankAccountController::class, 'edit'])->name('admin.account.edit')->middleware('PermissionCheck:account_update');
                Route::post('account/update', [BankAccountController::class, 'update'])->name('admin.account.update')->middleware('PermissionCheck:account_update');

                Route::get('account-view/{id}', [BankAccountController::class, 'view'])->name('admin.account.view')->middleware('PermissionCheck:account_view');
                Route::get('account-statement/{id}', [BankAccountController::class, 'statement'])->name('admin.account.statement')->middleware('PermissionCheck:account_statement');
                Route::get('staff/account-statement/{id}', [BankAccountController::class, 'staffStatement'])->name('staff.account.statement');

                Route::get('fund-transfer', [FundTransferController::class, 'index'])->name('admin.fund-transfer')->middleware('PermissionCheck:fund_transfer_read');
                Route::get('fund-transfer/create', [FundTransferController::class, 'create'])->name('admin.fund-transfer.create')->middleware('PermissionCheck:fund_transfer_create');
                Route::post('fund-transfer/store', [FundTransferController::class, 'store'])->name('admin.fund-transfer.store')->middleware('PermissionCheck:fund_transfer_create');
                Route::get('fund-transfer/edit/{id}', [FundTransferController::class, 'edit'])->name('admin.fund-transfer.edit')->middleware('PermissionCheck:fund_transfer_update');
                Route::post('fund-transfer/update', [FundTransferController::class, 'update'])->name('admin.fund-transfer.update')->middleware('PermissionCheck:fund_transfer_update');
                Route::delete('fund-transfer/delete/{id}', [FundTransferController::class, 'delete'])->middleware('PermissionCheck:fund_transfer_delete');

                Route::get('withdraws/{id?}', [AdminWithdrawController::class, 'index'])->name('admin.withdraws')->middleware('PermissionCheck:withdraw_read');
                Route::get('withdraw/create', [AdminWithdrawController::class, 'create'])->name('admin.withdraw.create')->middleware('PermissionCheck:withdraw_create');
                Route::post('withdraw/store', [AdminWithdrawController::class, 'store'])->name('admin.withdraw.store')->middleware('PermissionCheck:withdraw_create');
                Route::get('withdraw/edit/{id}', [AdminWithdrawController::class, 'edit'])->name('admin.withdraw.edit')->middleware('PermissionCheck:withdraw_update');
                Route::post('withdraw/update', [AdminWithdrawController::class, 'update'])->name('admin.withdraw.update')->middleware('PermissionCheck:withdraw_update');
                Route::get('withdraw/details/{id}', [AdminWithdrawController::class, 'details'])->name('admin.withdraw.details')->middleware('PermissionCheck:withdraw_read');
                Route::get('withdraw/invoice/{id}', [AdminWithdrawController::class, 'invoice'])->name('admin.withdraw.invoice')->middleware('PermissionCheck:withdraw_read');
                Route::get('withdraw/invoice/print/{id}', [AdminWithdrawController::class, 'print'])->name('admin.withdraw.invoice.print')->middleware('PermissionCheck:withdraw_read');

                //bulk payments
                Route::get('bulk/withdraw', [BulkWithdrawController::class, 'index'])->name('admin.withdraws.bulk')->middleware('PermissionCheck:bulk_withdraw_read');
                Route::get('bulk/withdraw/create', [BulkWithdrawController::class, 'create'])->name('admin.withdraws.bulk.create')->middleware('PermissionCheck:bulk_withdraw_create');
                Route::post('bulk/withdraw/store', [BulkWithdrawController::class, 'store'])->name('admin.withdraws.bulk.store')->middleware('PermissionCheck:bulk_withdraw_create');
                Route::get('bulk/withdraw/edit/{id}', [BulkWithdrawController::class, 'edit'])->name('admin.withdraws.bulk.edit')->middleware('PermissionCheck:bulk_withdraw_update');
                Route::get('withdraw/get-batches', [BulkWithdrawController::class, 'batches'])->name('get-batches')->middleware('PermissionCheck:add_to_bulk_withdraw');
                Route::post('bulk/withdraw/withdraw-update', [BulkWithdrawController::class, 'update'])->name('admin.withdraws.bulk.update')->middleware('PermissionCheck:bulk_withdraw_update');
                Route::post('bulk/withdraw/process-payment', [BulkWithdrawController::class, 'processPayment'])->name('admin.bulk.process-payment')->middleware('PermissionCheck:bulk_withdraw_process');
                Route::get('bulk/withdraw/payment-invoice/{id}', [BulkWithdrawController::class, 'invoice'])->name('admin.withdraw.invoice.bulk')->middleware('PermissionCheck:bulk_withdraw_read');
                Route::delete('bulk-withdraw-delete/{id}', [BulkWithdrawController::class, 'delete'])->middleware('PermissionCheck:bulk_withdraw_delete');


                Route::get('get-merchant-info', [AdminWithdrawController::class, 'getMerchantInfo'])->middleware('PermissionCheck:merchant_read');
                Route::post('withdraw-status/{id}/{status}', [AdminWithdrawController::class, 'chargeStatus'])->middleware('PermissionCheck:withdraw_process', 'PermissionCheck:withdraw_reject');

                Route::post('process-payment', [AdminWithdrawController::class, 'processPayment'])->name('process-payment')->middleware('PermissionCheck:withdraw_process');
                Route::post('approve-payment', [AdminWithdrawController::class, 'approvePayment'])->name('approve-payment')->middleware('PermissionCheck:withdraw_process');
                Route::post('reject-payment', [AdminWithdrawController::class, 'rejectPayment'])->name('reject-payment')->middleware('PermissionCheck:withdraw_reject');
                Route::post('update-payment-batch', [AdminWithdrawController::class, 'updateBatch'])->name('update-payment-batch')->middleware('PermissionCheck:add_to_bulk_withdraw');

                //reports
                Route::get('reports/transaction-history', [ReportController::class, 'transactionHistory'])->name('admin.transaction_history')->middleware('PermissionCheck:transaction_history_read');
                Route::any('transactions', [ReportController::class, 'transactionSearch'])->name('admin.search.transaction')->middleware('PermissionCheck:transaction_history_read');
                Route::get('reports/parcels', [ReportController::class, 'parcels'])->name('admin.parcels')->middleware('PermissionCheck:parcels_summary_read');
                Route::any('reports/search-parcels', [ReportController::class, 'parcelSearch'])->name('admin.search.parcels')->middleware('PermissionCheck:parcels_summary_read');
                Route::get('reports/merchant-summary', [ReportController::class, 'merchantReport'])->name('admin.merchant.summary')->middleware('PermissionCheck:merchant_summary_report_read');
                Route::any('reports/merchant-summary-report', [ReportController::class, 'merchantReportSearch'])->name('admin.search.merchant.summary')->middleware('PermissionCheck:merchant_summary_report_read');
                Route::get('reports/income-expense', [ReportController::class, 'incomeExpense'])->name('admin.income.expense')->middleware('PermissionCheck:income_expense_report_read');
                Route::any('reports/search-income-expense', [ReportController::class, 'incomeExpenseSearch'])->name('admin.search.income.expense')->middleware('PermissionCheck:income_expense_report_read');
                Route::get('reports/total-summary', [ReportController::class, 'totalSummery'])->name('admin.total_summery')->middleware('PermissionCheck:total_summary_read');
                Route::any('reports/total-summary-report', [ReportController::class, 'totalSummerySearch'])->name('admin.total_summery.report')->middleware('PermissionCheck:total_summary_read');

                //  sms preference
                Route::get('sms/sms-preference', [PreferenceController::class, 'smsPreference'])->name('sms.preference.setting')->middleware('PermissionCheck:sms_setting_read');
                Route::post('sms/sms-status', [PreferenceController::class, 'statusChange'])->name('sms.sms-status')->middleware('PermissionCheck:sms_setting_update');
                Route::post('sms/sms-masking-status', [PreferenceController::class, 'maskingStatusChange'])->name('sms.sms-masking-status')->middleware('PermissionCheck:sms_setting_update');
                Route::get('sms/sms-setting', [SettingsController::class, 'sms'])->name('sms.setting');

                //  live search
                Route::get('get-merchant-live', [LiveSearchController::class, 'getMerchant'])->name('get-merchant-live');
                Route::get('get-delivery-man-live', [LiveSearchController::class, 'getDeliveryMan'])->name('get-delivery-man-live');
                Route::get('get-user-man-live', [LiveSearchController::class, 'getUser'])->name('get-user-live');
                Route::get('get-parcel-live', [LiveSearchController::class, 'getParcel'])->name('get-parcel-live');
                Route::get('get-third-party-live', [LiveSearchController::class, 'getThirdParty'])->name('get-third-party-live');

                Route::get('parcel/bulk/assigning-pickup-man', [BulkController::class, 'createPickup'])->name('bulk.pickup.assign')->middleware('PermissionCheck:parcel_pickup_assigned');
                Route::get('parcel/bulk/get-merchant-parcels', [BulkController::class, 'getParcels'])->name('bulk.assigning.parcel.pickup');
                Route::post('parcel/bulk/assign-pickup-save', [BulkController::class, 'bulkPickupAssign'])->name('bulk.pickup-assigning.parcel.save');
                //bulk branch transfer routes
                Route::get('parcel/bulk/transfer-to-branch', [BulkController::class, 'bulkTransferCreate'])->name('bulk.transfer')->middleware('PermissionCheck:parcel_transfer_to_branch');
                Route::get('parcel/bulk/add-transfer-parcel-row/{parcel_no}', [BulkController::class, 'transferAdd'])->name('bulk.transfer.parcel');
                Route::post('parcel/bulk/parcel-transfer-to-branch-save', [BulkController::class, 'bulkTransferSave'])->name('bulk.transfer.parcel.save');
                //bulk transfer receive to branch routes
                Route::get('parcel/transfer-receive-to-branch', [BulkController::class, 'bulkTransferReceive'])->name('bulk.transfer.receive')->middleware('PermissionCheck:parcel_transfer_to_branch');
                Route::get('parcel/bulk/add-receive-parcel-row/{parcel_no}', [BulkController::class, 'transferReceive'])->name('bulk.transfer.receive.parcel');
                Route::post('parcel-transfer-to-branch-receive', [BulkController::class, 'bulkTransferReceivePost'])->name('bulk.transfer.parcel.receive');
                //payment filter
                Route::any('payment-filter', [AdminWithdrawController::class, 'filter'])->name('admin.payment.filter');

                Route::get('reverse-options', [ParcelController::class, 'reverseOptions'])->name('parcel.reverse.options');
                Route::get('transfer-options', [ParcelController::class, 'transferOptions'])->name('parcel.transfer.options');
                Route::get('parcel-reverse-from-cancel/{id}/{status}', [ParcelController::class, 'reverseUpdate']);
                //branch routes
                Route::get('branches', [BranchController::class, 'index'])->name('admin.branch')->middleware('PermissionCheck:branch_read');
                Route::get('branch/create', [BranchController::class, 'create'])->name('admin.branch.create')->middleware('PermissionCheck:branch_create');
                Route::post('branch/store', [BranchController::class, 'store'])->name('admin.branch.store')->middleware('PermissionCheck:branch_create');
                Route::get('branch/edit/{id}', [BranchController::class, 'edit'])->name('admin.branch.edit')->middleware('PermissionCheck:branch_update');
                Route::post('branch/update', [BranchController::class, 'update'])->name('admin.branch.update')->middleware('PermissionCheck:branch_update');
                Route::delete('branch/delete/{id}', [BranchController::class, 'delete'])->middleware('PermissionCheck:branch_delete');
                Route::post('branch/update-status', [BranchController::class, 'statusChange'])->name('admin.branch.update-status');
                Route::post('default/branch', [BranchController::class, 'defaultBranch'])->name('admin.default.branch')->middleware('PermissionCheck:branch_update');

                //payment method
                Route::get('setting/payment-method', [PaymentMethodController::class, 'index'])->name('admin.payment.method');
                Route::get('setting/payment-method/create', [PaymentMethodController::class, 'create'])->name('admin.payment.method.create');
                Route::post('setting/payment-method/store', [PaymentMethodController::class, 'store'])->name('admin.payment.method.store');
                Route::get('setting/payment-method/edit/{id}', [PaymentMethodController::class, 'edit'])->name('admin.edit.payment.method');
                Route::post('setting/payment-method/update/{id}', [PaymentMethodController::class, 'update'])->name('admin.payment.method.update');
                Route::delete('setting/payment-method/delete/{id}', [PaymentMethodController::class, 'delete']);
                Route::post('setting/payment-method/update-charge', [PaymentMethodController::class, 'statusChange'])->name('admin.payment-method.update-status');


                Route::get('get-current-cod', [ParcelController::class, 'parcelCod'])->middleware('PermissionCheck:parcel_delivered');
                Route::any('invoice-filter', [AdminWithdrawController::class, 'filterByMerchantName'])->name('admin.invoice.filter');
                Route::get('logout-user-all-devices/{id}', [UserController::class, 'logoutUserDevices'])->name('logout.user.all.devices')->middleware('PermissionCheck:user_logout_from_devices');
                //settings
                Route::post('setting-store', [SettingsController::class, 'store'])->name('setting.store');
                Route::post('packaging.and.charge.update', [SettingsController::class, 'packagingChargeUpdate'])->name('packaging.and.charge.update')->middleware('PermissionCheck:charge_setting_update');
                Route::get('pagination-setting', [SettingsController::class, 'pagination'])->name('pagination.setting');
                Route::get('setting/charges-setting', [SettingsController::class, 'charges'])->name('charges.setting');
                Route::get('setting/preference-setting', [SettingsController::class, 'preference'])->name('preference.setting');
                Route::get('setting/packaging-charge-setting', [SettingsController::class, 'packingCharge'])->name('packaging.charge.setting');
                Route::get('add-charge-packaging-row', [SettingsController::class, 'packingChargeAdd'])->name('add.charge.packaging.row');
                Route::get('add-charge-row', [SettingsController::class, 'chargeAdd'])->name('add.charge.row');
                Route::post('update-charge', [SettingsController::class, 'chargeUpdate'])->name('update.charge');
                Route::get('database-backup-storage-setting', [SettingsController::class, 'databaseBackupSetting'])->name('database.backup.storage.setting');
                Route::get('mobile-app-setting', [SettingsController::class, 'mobileAppSetting'])->name('mobile.app.setting');
                Route::post('delete-packaging-charge/{id}', [SettingsController::class, 'deletePackagingCharge'])->middleware('PermissionCheck:charge_setting_update');
                Route::post('preference-status', [SettingsController::class, 'statusChange'])->name('admin.preference-status')->middleware('PermissionCheck:preference_setting_update');

                //notice
                Route::get('notice', [NoticeController::class, 'index'])->name('notice')->middleware('PermissionCheck:notice_read');
                Route::get('notice/create', [NoticeController::class, 'create'])->name('notice.create')->middleware('PermissionCheck:notice_create');
                Route::post('notice/store', [NoticeController::class, 'store'])->name('notice.store')->middleware('PermissionCheck:notice_create');
                Route::get('notice/edit/{id}', [NoticeController::class, 'edit'])->name('notice.edit')->middleware('PermissionCheck:notice_update');
                Route::post('notice/update', [NoticeController::class, 'update'])->name('notice.update')->middleware('PermissionCheck:notice_update');
                Route::post('notice/status', [NoticeController::class, 'statusChange'])->name('admin.notice.status')->middleware('PermissionCheck:notice_update');
                Route::delete('notice/delete/{id}', [NoticeController::class, 'delete'])->middleware('PermissionCheck:notice_delete');
                // percel otp logs
                Route::controller(PercelOtpController::class)->prefix('percel-otp')->group(function () {
                    Route::get('/', 'index')->name('percel-otp.index')->middleware('PermissionCheck:percel_otp_read');
                    Route::delete('/delete/{id}', 'delete')->name('percel-otp.delete')->middleware('PermissionCheck:percel_otp_delete');
                });

               ////Audit log

               Route::controller(AuditLogController::class)->prefix('audit-logs')->group(function () {
                   Route::get('/', 'index')->name('audit.logs')->middleware('PermissionCheck:audit_log_read');
                 
               });

               /////

                Route::controller(RiderReportController::class)->prefix('rider-reports')->group(function(){
                    Route::get('/', 'index')->name('admin.rider.report')->middleware('PermissionCheck:rider_report_read');
                    Route::get('/export-csv', 'exportCsv')->name('admin.rider.report.csv')->middleware('PermissionCheck:rider_report_read');
                });

                // branch hub report

                Route::controller(HubReportController::class)->prefix('branch-hub-reports')->group(function(){
                    Route::get('/', 'index')->name('admin.branch_hub.report')->middleware('PermissionCheck:hub_report_read');
                    Route::get('/export-csv', 'exportCsv')->name('admin.hub.report.csv')->middleware('PermissionCheck:hub_report_read');
                });
                
              //Financial Report
                Route::controller(FinancialController::class)->prefix('financial-reports')->group(function(){
                    Route::get('/', 'index')->name('admin.financial.report')->middleware('PermissionCheck:financial_report_read');
                    Route::get('/export-csv', 'exportCsv')->name('admin.financial.report.csv')->middleware('PermissionCheck:financial_report_read');
                });

                //third party routes
                Route::get('third-parties', [ThirdPartyController::class, 'index'])->name('admin.third-parties')->middleware('PermissionCheck:third_party_read');
                Route::get('third-party/create', [ThirdPartyController::class, 'create'])->name('admin.third-party.create')->middleware('PermissionCheck:third_party_create');
                Route::post('third-party/store', [ThirdPartyController::class, 'store'])->name('admin.third-party.store')->middleware('PermissionCheck:third_party_create');
                Route::get('third-party/edit/{id}', [ThirdPartyController::class, 'edit'])->name('admin.third-party.edit')->middleware('PermissionCheck:third_party_update');
                Route::post('third-party/update', [ThirdPartyController::class, 'update'])->name('admin.third-party.update')->middleware('PermissionCheck:third_party_update');
                Route::delete('third-party/delete/{id}', [ThirdPartyController::class, 'delete'])->middleware('PermissionCheck:third_party_delete');
                Route::POST('third-party/status', [ThirdPartyController::class, 'changeStatus'])->name('admin.third-party.status')->middleware('PermissionCheck:third_party_update');
                Route::get('get-parcel-location', [ParcelController::class, 'location']);
                Route::get('closing-report/{id}', [ParcelController::class, 'download'])->name('admin.merchant.closing.report')->middleware('PermissionCheck:download_closing_report');
                Route::get('batch-payment/{id}', [BulkWithdrawController::class, 'download'])->name('admin.payment.report')->middleware('PermissionCheck:download_payment_sheet');
                Route::delete('remove-from-batch/{id}', [AdminWithdrawController::class, 'remove'])->name('admin.payment.remove.payment')->middleware('PermissionCheck:add_to_bulk_withdraw');

                //mobile app setting
                Route::resource('apikeys', ApiKeyController::class)->except(['show']);
                Route::post('apikeys/revoke', [ApiKeyController::class, 'revoke'])->name('apikeys.revoke');

                /*-----============ Email setting ========================= */
                Route::group(['as' => 'email.'], function () {
                    Route::get('email/server-configuration', [EmailController::class, 'serverConfiguration'])->name('server-configuration');
                    Route::put('email/server-configuration', [EmailController::class, 'serverConfigurationUpdate'])->name('server-configuration.update');
                    Route::post('test/email', [EmailController::class, 'sendTestMail'])->name('test');
                    Route::get('email/template', [EmailController::class, 'emailTemplate'])->name('template');
                    Route::put('email-template/update', [EmailController::class, 'emailTemplateUpdate'])->name('template.update');
                    Route::post('template-body', [EmailController::class, 'templateBody'])->name('template-body');
                });
                //system setting
                Route::get('setting/system-setting', [SystemSettingController::class, 'generalSetting'])->name('general.setting');
                Route::post('setting/system-setting', [SystemSettingController::class, 'generalSettingUpdate'])->name('general.setting');

                // cron setting
                Route::get('cron-setting', [SystemSettingController::class, 'cronSetting'])->name('cron.setting');
                Route::post('cron-setting-update', [SystemSettingController::class, 'cronUpdate'])->name('admin.cron.update');

                /*------==== Notification ------------------======= */
                //pusher notification
                Route::get('setting/pusher-notification', [SystemSettingController::class, 'pusher'])->name('pusher.notification');
                Route::post('setting/pusher-notification', [SystemSettingController::class, 'savePusherSetting'])->name('pusher.notification');

                Route::resource('languages', LanguageController::class)->except(['show', 'update']);
                Route::post('languages/update/{id}', [LanguageController::class, 'update'])->name('languages.update');
                Route::get('language/translations', [LanguageController::class, 'translationPage'])->name('language.translations.page');
                Route::post('languages/{language}', [LanguageController::class, 'updateTranslations'])->name('admin.language.key.update');
                Route::post('language-status', [LanguageController::class, 'statusChange'])->name('admin.languages.language-status');
                Route::post('language-direction-change', [LanguageController::class, 'directionChange'])->name('admin.languages.language-direction-change');
                Route::match(['get', 'post'], 'app-setting', [HomeController::class, 'changeAppSetting'])->name('change.app.setting');
                //admin panel setting
                Route::get('setting/panel-setting', [SystemSettingController::class, 'adminPanelSetting'])->name('admin.panel-setting');
                Route::post('setting/panel-setting', [SystemSettingController::class, 'updateSetting'])->name('admin.panel-setting');
                Route::post('setting/font', [SystemSettingController::class, 'updateFont'])->name('admin.font');
                Route::get('setting/add/social-media', [SystemSettingController::class, 'adminSocialMedia'])->name('admin.add.social-media');
                Route::resource('setting/countries', CountryController::class)->except(['create', 'show', 'update']);
                Route::post('countries/update/{id}', [CountryController::class, 'update'])->name('countries.update');
                Route::post('countries-status', [CountryController::class, 'statusChange'])->name('admin.countries.countries-status');
                Route::resource('setting/divisions', DivisionController::class)->except(['create', 'show', 'update']);
                Route::post('divisions/update/{id}', [DivisionController::class, 'update'])->name('divisions.update');
                Route::post('divisions-status', [DivisionController::class, 'statusChange'])->name('admin.divisions.divisions-status');

                Route::controller(AreaController::class)->prefix('setting/areas')->name('areas.')->group(function () {
                    Route::get('/', 'index')->name('index')->middleware('PermissionCheck:area_read');
                    Route::post('/store', 'store')->name('store')->middleware('PermissionCheck:area_create');
                    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('PermissionCheck:area_update');
                    Route::post('/update/{id}', 'update')->name('update')->middleware('PermissionCheck:area_update');
                    Route::delete('/destroy/{id}', 'destroy')->name('destroy')->middleware('PermissionCheck:area_delete');
                    Route::post('/status-change', 'statusChange')->name('status-change')->middleware('PermissionCheck:area_update');
                    Route::post('/import', 'import')->name('import')->middleware('PermissionCheck:area_create');
                    Route::get('/download-sample', 'downloadSample')->name('download-sample');
                });
                Route::post('areas-status', [AreaController::class, 'statusChange'])->name('admin.areas.areas-status');
                
              

                Route::controller(BagController::class)->prefix('bag')->name('bag.')->group(function () {
                    Route::get('/', 'index')->name('index')->middleware('PermissionCheck:bag_read');
                    Route::post('/store', 'store')->name('store')->middleware('PermissionCheck:bag_create');
                    Route::get('/show/{id}', 'show')->name('show')->middleware('PermissionCheck:bag_read');
                    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('PermissionCheck:bag_update');
                    Route::post('/update/{id}', 'update')->name('update')->middleware('PermissionCheck:bag_update');
                    Route::delete('/destroy/{id}', 'destroy')->name('destroy')->middleware('PermissionCheck:bag_delete');
                    
                    Route::post('/add-parcel', 'addParcel')->name('add-parcel')->middleware('PermissionCheck:bag_update');
                    Route::post('/remove-parcel/{id}', 'removeParcel')->name('remove-parcel')->middleware('PermissionCheck:bag_update');
                    
                    Route::post('/close/{id}', 'closeBag')->name('close')->middleware('PermissionCheck:bag_update');
                    Route::post('/dispatch/{id}', 'dispatchBag')->name('dispatch')->middleware('PermissionCheck:bag_update');
                    Route::post('/receive/{id}', 'receiveBag')->name('receive')->middleware('PermissionCheck:bag_update');
                    
                    Route::get('/print-manifest/{id}', 'printManifest')->name('print-manifest')->middleware('PermissionCheck:bag_read');
                    Route::get('/barcode/{id}', 'barcode')->name('barcode')->middleware('PermissionCheck:bag_read');
                });
            
                  /////////////////crm history //////////////

                  Route::controller(CrmHistoryController::class)->prefix('crm-history')->name('crm-history.')->group(function () {
                    Route::get('/', 'index')->name('index')->middleware('PermissionCheck:crm_history_read');
                    Route::post('/search','search')->name('crm.search')->middleware('PermissionCheck:crm_history_read');
                   
                  });



                //district
                Route::controller(DistrictController::class)->prefix('setting/districts')->name('districts.')->group(function () {
                    Route::get('/', 'index')->name('index')->middleware('PermissionCheck:district_read');
                    Route::post('/store', 'store')->name('store')->middleware('PermissionCheck:district_create');
                    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('PermissionCheck:district_update');
                    Route::post('/update/{id}', 'update')->name('update')->middleware('PermissionCheck:district_update');
                    Route::delete('/destroy/{id}', 'destroy')->name('destroy')->middleware('PermissionCheck:district_delete');
                    Route::post('/status-change', 'statusChange')->name('status-change')->middleware('PermissionCheck:district_update');
                });
                Route::post('districts-status', [DistrictController::class, 'statusChange'])->name('admin.districts.districts-status');
                
                
                Route::controller(ThanaUpazilaController::class)->prefix('setting/upazilas')->name('upazilas.')->group(function () {
                    Route::get('/', 'index')->name('index')->middleware('PermissionCheck:upazila_read');
                    Route::post('/store', 'store')->name('store')->middleware('PermissionCheck:upazila_create');
                    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('PermissionCheck:upazila_update');
                    Route::post('/update/{id}', 'update')->name('update')->middleware('PermissionCheck:upazila_update');
                    Route::delete('/destroy/{id}', 'destroy')->name('destroy')->middleware('PermissionCheck:upazila_delete');
                    Route::post('/status-change', 'statusChange')->name('status-change')->middleware('PermissionCheck:upazila_update');
                });
                Route::post('upazilas-status', [ThanaUpazilaController::class, 'statusChange'])->name('admin.upazilas.upazilas-status');

                Route::controller(DeliveryZoneController::class)->prefix('setting/delivery-zones')->name('delivery-zones.')->group(function () {
                    Route::get('/', 'index')->name('index')->middleware('PermissionCheck:delivery_zone_read');
                    Route::post('/store', 'store')->name('store')->middleware('PermissionCheck:delivery_zone_create');
                    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('PermissionCheck:delivery_zone_update');
                    Route::post('/update/{id}', 'update')->name('update')->middleware('PermissionCheck:delivery_zone_update');
                    Route::delete('/destroy/{id}', 'destroy')->name('destroy')->middleware('PermissionCheck:delivery_zone_delete');
                    Route::post('/status-change', 'statusChange')->name('status-change')->middleware('PermissionCheck:delivery_zone_update');
                });
                Route::post('delivery-zones-status', [DeliveryZoneController::class, 'statusChange'])->name('admin.delivery-zones.delivery-zones-status');

                //system update
                Route::get('utility/system-update', [UtilityController::class, 'systemUpdate'])->name('system.update');
                Route::post('utility/system-update', [UtilityController::class, 'downloadUpdate'])->name('system.update');
                Route::get('utility/server-info', [UtilityController::class, 'serverInfo'])->name('server.info');
                Route::get('utility/system-info', [UtilityController::class, 'serverInfo'])->name('system.info');
                Route::get('utility/extension-library', [UtilityController::class, 'serverInfo'])->name('extension.library');
                Route::get('utility/file-system-permission', [UtilityController::class, 'serverInfo'])->name('file.system.permission');

                Route::get('otp-setting', [PreferenceController::class, 'otpSetting'])->name('otp.setting');
                Route::get('otp-status', [PreferenceController::class, 'otpStatusChange'])->name('otp-status');
                Route::post('otp-setting', [PreferenceController::class, 'saveOTP'])->name('otp.setting');
                Route::match(['get', 'post'], 'test-number-send', [PreferenceController::class, 'sendNumber'])->name('test.number.send');
                Route::get('sms-templates', [PreferenceController::class, 'smsTemplates'])->name('sms.templates');
                Route::post('sms-template', [PreferenceController::class, 'saveTemplate'])->name('save.template');

                //website setting
                Route::get('website/theme-options', [WebsiteSettingController::class, 'themeOptions'])->name('admin.theme.options');
                Route::post('website/theme-options', [WebsiteSettingController::class, 'updateThemesOptions'])->name('admin.theme.options');

                //website menu
                Route::get('website/menu', [HeaderSettingController::class, 'headerMenu'])->name('admin.menu');
                Route::post('website/menu', [HeaderSettingController::class, 'updateHeaderMenu'])->name('admin.update-menu');

                //website title subtitle
                Route::get('website/section-title-subtitle', [WebsiteSettingController::class, 'sectionTitleSubtitle'])->name('admin.section_title_subtitle');
                Route::post('website/section-title-subtitle', [WebsiteSettingController::class, 'updateSectionTitleSubtitle'])->name('admin.section_title_subtitle');

                //website hero
                Route::get('website/hero-section', [WebsiteSettingController::class, 'heroSection'])->name('admin.hero.section');
                Route::post('website/hero-section', [WebsiteSettingController::class, 'updateHeroSection'])->name('admin.hero.section');


                //website contact
                Route::get('website/contact', [WebsiteSettingController::class, 'contactSection'])->name('admin.contact.section');
                Route::post('website/contact', [WebsiteSettingController::class, 'updateContactSection'])->name('admin.contact.section');

                //website pricing
                Route::get('website/pricing', [WebsiteSettingController::class, 'pricingSection'])->name('admin.pricing.section');
                Route::post('website/pricing', [WebsiteSettingController::class, 'updatePricingSection'])->name('admin.pricing.section');

                //website partner logo
                Route::resource('website/partner-logo', WebsitePartnerLogoController::class);
                Route::post('partner-logo-status', [WebsitePartnerLogoController::class, 'statusChange'])->name('partner-logo-status.change');

                //website ai chat
                Route::get('website/ai-chat', [WebsiteSettingController::class, 'aiChatSection'])->name('admin.ai.chat');
                Route::post('website/ai-chat', [WebsiteSettingController::class, 'updateAiChatSection'])->name('admin.ai.chat');

                //testimonials
                Route::resource('website/testimonials', WebsiteTestimonialController::class)->except(['show']);
                Route::post('testimonial-status', [WebsiteTestimonialController::class, 'statusChange'])->name('testimonial.status.change');

                //news and event
                Route::resource('website/news-and-events', WebsiteNewsAndEventController::class)->except(['show']);
                Route::post('website/news-and-event-status', [WebsiteNewsAndEventController::class, 'statusChange'])->name('website.news-and-event.status.change');

                //service
                Route::resource('website/services', WebsiteServiceController::class)->except(['show']);
                Route::post('website/service-status', [WebsiteServiceController::class, 'statusChange'])->name('website.service.status.change');

                //statistic
                Route::get('website/statistic', [WebsiteSettingController::class, 'statisticSection'])->name('admin.statistic');
                Route::post('website/statistic', [WebsiteSettingController::class, 'updateStatisticSection'])->name('admin.statistic');

                //about
                Route::resource('website/abouts', WebsiteAboutController::class)->except(['show']);
                Route::post('website/about-status', [WebsiteAboutController::class, 'statusChange'])->name('website.about.status.change');
                Route::post('website/about-image', [WebsiteAboutController::class, 'imageUpdate'])->name('about.image');

                //feature
                Route::resource('website/features', WebsiteFeatureController::class)->except(['show']);
                Route::post('website/feature-status', [WebsiteFeatureController::class, 'statusChange'])->name('website.feature.status.change');


                //website cta
                Route::get('website/cta', [WebsiteSettingController::class, 'ctaSection'])->name('admin.cta');
                Route::post('website/cta', [WebsiteSettingController::class, 'updateCTASection'])->name('admin.cta');

                //website footer-content
                Route::get('website/footer-content', [FooterSettingController::class, 'footerContent'])->name('footer.content');

                Route::get('website/primary-content-setting', [FooterSettingController::class, 'primaryContentSetting'])->name('footer.primary-content');

                Route::post('website/primary-content-setting', [FooterSettingController::class, 'saveSocialLinkSetting'])->name('footer.primary-content');

                Route::get('website/useful-link-setting', [FooterSettingController::class, 'usefulLinkSetting'])->name('footer.useful-links');

                Route::get('website/quick-link-setting', [FooterSettingController::class, 'quickLinkSetting'])->name('footer.quick-links');

                Route::get('website/app', [FooterSettingController::class, 'appSetting'])->name('footer.app');

                Route::get('website/copyright-setting', [FooterSettingController::class, 'copyrightSetting'])->name('footer.copyright');

                Route::post('website/update-footer-setting', [FooterSettingController::class, 'updateSetting'])->name('footer.update-setting');
                Route::post('website/update-footer-menu', [FooterSettingController::class, 'menuUpdate'])->name('footer.update-menu');

                //website seo
                Route::get('website-seo', [WebsiteSettingController::class, 'seo'])->name('website.seo');
                Route::post('website-seo', [WebsiteSettingController::class, 'saveSeoSetting'])->name('website.seo');

                //website seo
                Route::get('google-setup', [WebsiteSettingController::class, 'google'])->name('google.setup');
                Route::post('google-setup', [WebsiteSettingController::class, 'saveGoogleSetup'])->name('google.setup');

                //website custom js and css
                Route::get('custom-js', [WebsiteSettingController::class, 'customJs'])->name('custom.js');
                Route::get('custom-css', [WebsiteSettingController::class, 'customCss'])->name('custom.css');
                Route::post('custom-css', [WebsiteSettingController::class, 'saveCustomCssAndJs'])->name('custom.css.js');

                //website facebook pixel
                Route::get('facebook-pixel', [WebsiteSettingController::class, 'fbPixel'])->name('fb.pixel');
                Route::post('facebook-pixel', [WebsiteSettingController::class, 'saveFbPixel'])->name('fb.pixel');

                //website page
                Route::resource('website/pages', WebsitePageController::class)->except(['show', 'update']);
                Route::post('pages/update/{id}', [WebsitePageController::class, 'update'])->name('pages.update');
                Route::post('pages-status', [WebsitePageController::class, 'statusChange'])->name('page.status.change');



                // route to check if the status of parcel is suitable(e.g: received)
                Route::post('/parcels/check-received', [ParcelController::class, 'checkReceived'])->name('parcels.checkReceived');
                // route to check if the status of parcel is suitable(e.g: received)
                Route::post('/parcels/check-delivery-assigned', [ParcelController::class, 'checkDeliveryAssigned'])->name('parcels.checkDeliveryAssigned');
                // route to check if the status of parcel is suitable(e.g: received)
                Route::post('/parcels/check-picked-up', [ParcelController::class, 'checkPickedUp'])->name('parcels.checkPickedUp');
                // route to check if the status of parcel is suitable(e.g: pickup-assigned)
                Route::post('/parcels/check-assigned-pick-up', [ParcelController::class, 'checkAssignedPickupman'])->name('parcels.checkAssignedPickupman');

                Route::post('/parcels/check-return-to-warehouse', [ParcelController::class, 'checkReturnToWarehouse'])->name('parcels.checkReturnToWarehouse');

                Route::post('/parcels/check-return-assign-to-merchant', [ParcelController::class, 'checkReturnAssignToMerchant'])->name('parcels.checkReturnAssignToMerchant');

            });
        });

        Route::middleware(['LoginCheckMerchant'])->group(function () {
            //merchant routes
            Route::prefix('merchant')->group(function () {
                route::get('dashboard', [MerchantDashboardController::class, 'index'])->name('merchant.dashboard');
                route::get('logout', [AuthController::class, 'logout'])->name('merchant.logout');
                Route::get('notification/update/{id}', [NotificationController::class, 'index'])->name('merchant.notification.update');
                //merchant product routes
                Route::get('products', [ProductController::class, 'index'])->name('merchant.products');
                Route::post('products/store', [ProductController::class, 'store'])->name('merchant.products.store');
                Route::POST('products/status', [ProductController::class, 'statusChange'])->name('merchant.products.status');
                Route::get('products/edit/{id}', [ProductController::class, 'edit'])->name('merchant.products.edit');
                Route::post('products/update/{id}', [ProductController::class, 'update'])->name('merchant.products.update');
                Route::delete('products/delete/{id}', [ProductController::class, 'delete'])->name('merchant.products.delete');


                //merchant product routes
                Route::get('stock/{type}', [StockController::class, 'index'])->name('merchant.stock.list');
                Route::get('stock-history', [StockController::class, 'stockHistory'])->name('merchant.stock.history');
                Route::post('stock-in/store', [StockController::class, 'store'])->name('merchant.stock.store');

                //merchant warehouse routes
                Route::get('warehouse', [WarehouseController::class, 'index'])->name('merchant.warehouse');
                Route::post('warehouse/store', [WarehouseController::class, 'store'])->name('merchant.warehouse.store');
                Route::delete('warehouse/delete/{id}', [WarehouseController::class, 'delete'])->name('merchant.warehouse.delete');
                Route::POST('warehouse/status', [WarehouseController::class, 'statusChange'])->name('merchant.warehouse.status');
                Route::get('warehouse/edit/{id}', [WarehouseController::class, 'edit'])->name('merchant.warehouse.edit');
                Route::post('warehouse/update/{id}', [WarehouseController::class, 'update'])->name('merchant.warehouse.update');

                //merchant profile routes
                Route::get('profile', [ProfileController::class, 'profile'])->name('merchant.profile');
                Route::get('company', [ProfileController::class, 'company'])->name('merchant.company');
                Route::get('notifications', [ProfileController::class, 'notification'])->name('merchant.notifications');
                Route::get('account-activity', [ProfileController::class, 'accountActivity'])->name('merchant.account-activity');
                Route::get('security-settings', [ProfileController::class, 'securitySetting'])->name('merchant.security-settings');
                Route::post('change-password', [ProfileController::class, 'changePassword'])->name('merchant.change-password');
                Route::post('profile-update', [ProfileController::class, 'profileUpdate'])->name('merchant.update.profile');
                Route::post('merchant-update', [ProfileController::class, 'merchantUpdate'])->name('merchant.update.merchant');
                Route::get('statements', [ProfileController::class, 'statements'])->name('merchant.statements');
                Route::get('shops', [ProfileController::class, 'shops'])->name('merchant.shops');
                Route::post('shop/store', [ProfileController::class, 'shopStore'])->name('merchant.add.shop');
                Route::get('shop/edit', [ProfileController::class, 'shopEdit'])->name('merchant.edit.shop');
                Route::post('shop/update', [ProfileController::class, 'shopUpdate'])->name('merchant.update.shop');
                Route::delete('shop/delete/{id}', [ProfileController::class, 'shopDelete']);
                //shop default status change
                Route::post('shop-default-update', [ProfileController::class, 'changeDefault'])->name('merchant.default.shop');
                Route::get('api-credentials', [ProfileController::class, 'apiCredentials'])->name('merchant.api.credentials');
                Route::post('api-credentials-update', [ProfileController::class, 'apiCredentialsUpdate'])->name('merchant.api.credentials.update')->middleware('PermissionCheck:update_api_credentials');
                //for getting shop phone number and address
                Route::get('shop', [ProfileController::class, 'shop'])->name('merchant.shop');
                //charges routes
                Route::get('charge', [ProfileController::class, 'charge'])->name('merchant.charge');
                Route::get('cod-charge', [ProfileController::class, 'codCharge'])->name('merchant.cod.charge');
                //merchant parcel routes
                Route::get('parcels', [MerchantParcelController::class, 'index'])->name('merchant.parcel');
                Route::get('parcel/request-parcel', [MerchantParcelController::class, 'create'])->name('merchant.parcel.create');
                Route::get('parcelbookthana', [MerchantParcelController::class, 'thanabook'])->name('merchant.parcel.thanabook');
                Route::post('parcel/store', [MerchantParcelController::class, 'store'])->name('merchant.parcel.store');
                Route::get('parcel/edit/{id}', [MerchantParcelController::class, 'edit'])->name('merchant.parcel.edit');
                Route::post('parcel/update', [MerchantParcelController::class, 'update'])->name('merchant.parcel.update');
                Route::any('parcel/filter', [MerchantParcelController::class, 'filter'])->name('merchant.parcel.filter');
                Route::get('parcel/detail/{id}', [MerchantParcelController::class, 'detail'])->name('merchant.parcel.detail');
                Route::post('parcel/download', [MerchantParcelController::class, 'getParcelDownload'])->name('merchant.parcel.download');
                Route::get('parcel/status-update/{id}/{status}', [MerchantParcelController::class, 'parcelStatusUpdate']);
                Route::get('parcel/print/{id}', [MerchantParcelController::class, 'print'])->name('merchant.parcel.print');
                Route::get('parcel/duplicate/{id}', [MerchantParcelController::class, 'duplicate'])->name('merchant.parcel.duplicate');
                //cancel parcel with note
                Route::post('parcel/cancel', [MerchantParcelController::class, 'parcelCancel'])->name('merchant.parcel-cancel');
                Route::post('parcel/delete', [MerchantParcelController::class, 'parcelDelete'])->name('merchant.parcel-delete');
                Route::post('parcel/re-request', [MerchantParcelController::class, 'parcelReRequest'])->name('merchant.parcel.re-request');
                Route::get('parcel-filtering/{slug}', [MerchantParcelController::class, 'parcelFiltering'])->name('merchant.parcel.filtering');

                Route::get('parcel/import', [ImportExportController::class, 'importExportView'])->name('merchant.import.csv');
                Route::post('parcel/import', [ImportExportController::class, 'import'])->name('merchant.import');
                Route::post('parcel/import-preview', [ImportExportController::class, 'preview'])->name('merchant.import.preview');
                Route::post('parcel/import-confirm', [ImportExportController::class, 'confirm'])->name('merchant.import.confirm');
                //merchant withdraw routes
                Route::get('withdraws', [MerchantWithdrawController::class, 'index'])->name('merchant.withdraw');
                Route::get('request-withdraw', [MerchantWithdrawController::class, 'create'])->name('merchant.withdraw.create');
                Route::post('request-withdraw', [MerchantWithdrawController::class, 'store'])->name('merchant.withdraw.store');
                // Route::get('request-withdraws', [MerchantWithdrawController::class, 'stores'])->name('merchant.withdraw.store');

                Route::get('edit-withdraw-request/{id}', [MerchantWithdrawController::class, 'edit'])->name('merchant.withdraw.edit');
                Route::post('update-withdraw-request', [MerchantWithdrawController::class, 'update'])->name('merchant.withdraw.update');
                Route::delete('withdraw-request/delete/{id}', [MerchantWithdrawController::class, 'delete']);
                Route::get('payment-invoice/{id}', [MerchantWithdrawController::class, 'invoice'])->name('merchant.invoice');
                Route::get('payment-invoice-print/{id}', [MerchantWithdrawController::class, 'invoicePrint'])->name('merchant.invoice.print');
                Route::get('withdraw-status/{id}/{status}', [MerchantWithdrawController::class, 'chargeStatus']);

                //merchant payment accounts routes
                Route::get('account/payment-method', [MerchantWithdrawController::class, 'paymentMethod'])->name('merchant.payment.method');
                Route::post('account/payment-method/update', [MerchantWithdrawController::class, 'updatePaymentMethod'])->name('merchant.update.payment.method');
                Route::get('account/payment', [MerchantWithdrawController::class, 'paymentAccounts'])->name('merchant.payment.accounts');
                Route::get('account/mfs', [MerchantWithdrawController::class, 'mfsAccounts'])->name('merchant.mfs.accounts');
                Route::post('account/bank/update', [MerchantWithdrawController::class, 'paymentBankUpdate'])->name('merchant.bank.account.update');
                Route::get('account/payment/others', [MerchantWithdrawController::class, 'paymentOthersAccount'])->name('merchant.payment.accounts.others');
                Route::post('account/others/update', [MerchantWithdrawController::class, 'paymentOthersAccountUpdate'])->name('merchant.others.account.update');

                //staff routes
                Route::get('staffs', [MerchantStaffController::class, 'index'])->name('merchant.staffs');
                Route::get('staff/create', [MerchantStaffController::class, 'create'])->name('merchant.staff.create');
                Route::post('staff/store', [MerchantStaffController::class, 'store'])->name('merchant.staff.store');
                Route::get('staff/edit/{id}', [MerchantStaffController::class, 'edit'])->name('merchant.staff.edit');
                Route::post('staff/update', [MerchantStaffController::class, 'update'])->name('merchant.staff.update');
                Route::POST('staff/user-status', [MerchantStaffController::class, 'statusChange'])->name('merchant.staff.user-status');
                Route::get('staff/personal-info/{id}', [MerchantStaffController::class, 'personalInfo'])->name('merchant.staff.personal.info');
                Route::get('staff/account-activity/{id}', [MerchantStaffController::class, 'accountActivity'])->name('merchant.staffs.account-activity');
                Route::get('logout-staff-all-devices/{id}', [UserController::class, 'logoutUserDevices']);
                Route::get('download-sample', [ImportExportController::class, 'export'])->name('merchant.export');
                Route::get('download', [MerchantParcelController::class, 'download'])->name('merchant.closing.report');

                // Merchant Customer CRM History
                Route::controller(CrmHistoryController::class)->prefix('crm-history')->name('merchant.crm-history.')->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/search', 'search')->name('search');
                });
            });
        });

        // Delivery Man / Rider Routes
        require base_path('routes/deliveryman.php');
    });

    Route::get('/batch/print', [ParcelController::class, 'batch_print'])->name('batch.print');
    Route::get('/export/parcel', [ParcelController::class, 'export_parcel'])->name('export.parcel');

    Route::get('old-migration', [DashboardController::class, 'oldMigration']);
    Route::get('track/{id}', [MerchantParcelController::class, 'track']);
    Route::get('charge-details', [ParcelController::class, 'chargeDetails']);
    Route::get('get-customer-info', [ParcelController::class, 'customerDetails']);
    Route::get('old-balance', [DashboardController::class, 'oldBalance']);
    Route::get('merge-update', [DashboardController::class, 'mergeUpdate']);
    Route::get('test-drive', function () {
        Storage::disk('google')->put('test.txt', 'Hello World');
    });
});
