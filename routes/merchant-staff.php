<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ImportExportController;
use App\Http\Controllers\MerchantStaff\ParcelController;
use App\Http\Controllers\MerchantStaff\ProfileController;
use App\Http\Controllers\MerchantStaff\WithdrawController;
use App\Http\Controllers\MerchantStaff\DashboardController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\NotificationController;

Route::group(['middleware'=>'XSS'], function() {
    Route::group(['prefix' => localeRoutePrefix()], function(){
        // Route::group(['prefix' => localeRoutePrefix()], function () {
    Route::middleware(['LoginCheckMerchantStaff'])->group(function (){
        route::get('dashboard', [DashboardController::class, 'index'])->name('merchant.staff.dashboard');
        Route::get('notification/update/{id}', [NotificationController::class, 'index'])->name('merchant.staff.notification.update');
        route::get('profile', [ProfileController::class, 'profile'])->name('merchant.staff.profile');
        route::get('company', [ProfileController::class, 'company'])->name('merchant.staff.company')->middleware('PermissionCheck:manage_company_information');
        Route::post('profile-update', [ProfileController::class, 'profileUpdate'])->name('merchant.staff.update.profile');
        Route::post('merchant-update', [ProfileController::class, 'merchantUpdate'])->name('merchant.staff.update.merchant')->middleware('PermissionCheck:manage_company_information');
        Route::get('account-activity', [ProfileController::class, 'accountActivity'])->name('merchant.staff.account-activity');
        Route::get('security-settings', [ProfileController::class, 'securitySetting'])->name('merchant.staff.security-settings');
        Route::post('change-password', [ProfileController::class, 'changePassword'])->name('merchant.staff.change-password');

        Route::get('charge', [ProfileController::class, 'charge'])->name('merchant.staff.charge')->middleware('PermissionCheck:delivery_charge');
        Route::get('cod-charge', [ProfileController::class, 'codCharge'])->name('merchant.staff.cod.charge')->middleware('PermissionCheck:cash_on_delivery_charge');
        Route::get('statements', [ProfileController::class, 'statements'])->name('merchant.staff.statements')->middleware('PermissionCheck:read_logs');
        Route::get('shops', [ProfileController::class, 'shops'])->name('merchant.staff.shops')->middleware('PermissionCheck:manage_shops');
        Route::get('shop', [ProfileController::class, 'shop'])->name('merchant.staff.shop');

        Route::post('shop-default-update', [ProfileController::class, 'changeDefault'])->name('merchant.staff.default.shop')->middleware('PermissionCheck:manage_shops');
        Route::post('shop-add', [ProfileController::class, 'shopStore'])->name('merchant.staff.add.shop')->middleware('PermissionCheck:manage_shops');
        Route::get('shop-edit', [ProfileController::class, 'shopEdit'])->name('merchant.staff.edit.shop')->middleware('PermissionCheck:manage_shops');
        Route::post('shop-update', [ProfileController::class, 'shopUpdate'])->name('merchant.staff.update.shop')->middleware('PermissionCheck:manage_shops');
        Route::delete('shop/delete/{id}', [ProfileController::class, 'shopDelete'])->middleware('PermissionCheck:manage_shops');

        route::get('logout', [AuthController::class, 'logout'])->name('merchant.staff.logout');
        Route::get('payment/method', [WithdrawController::class, 'staffPaymentMethod'])->name('merchant.staff.payment.method')->middleware('PermissionCheck:manage_payment_accounts');
        Route::post('/update/payment/method', [WithdrawController::class, 'updatePaymentMethod'])->name('merchant.staff.update.payment.method')->middleware('PermissionCheck:manage_payment_accounts');
        Route::get('payment/accounts', [WithdrawController::class, 'paymentAccounts'])->name('merchant.staff.payment.accounts')->middleware('PermissionCheck:manage_payment_accounts');
        Route::get('mfs/accounts', [WithdrawController::class, 'mfsAccounts'])->name('merchant.staff.mfs.accounts')->middleware('PermissionCheck:manage_payment_accounts');
        Route::post('bank/update', [WithdrawController::class, 'paymentBankUpdate'])->name('merchant.staff.bank.account.update')->middleware('PermissionCheck:manage_payment_accounts');
        Route::post('others/account/update', [WithdrawController::class, 'paymentOthersAccountUpdate'])->name('merchant.staff.others.account.update')->middleware('PermissionCheck:manage_payment_accounts');

        Route::get('parcels', [ParcelController::class, 'index'])->name('merchant.staff.parcel')->middleware('PermissionCheck:manage_parcel');
        Route::get('request-parcel', [ParcelController::class, 'create'])->name('merchant.staff.parcel.create')->middleware('PermissionCheck:manage_parcel');
        Route::post('parcel-store', [ParcelController::class, 'store'])->name('merchant.staff.parcel.store')->middleware('PermissionCheck:manage_parcel');
        Route::get('parcel-duplicate/{id}', [ParcelController::class, 'duplicate'])->name('merchant.staff.parcel.duplicate')->middleware('PermissionCheck:manage_parcel');
        Route::get('parcel-detail/{id}', [ParcelController::class, 'detail'])->name('merchant.staff.parcel.detail')->middleware('PermissionCheck:manage_parcel');
        Route::get('parcel-print/{id}', [ParcelController::class, 'print'])->name('merchant.staff.parcel.print')->middleware('PermissionCheck:manage_parcel');
        Route::get('parcel-edit/{id}', [ParcelController::class, 'edit'])->name('merchant.staff.parcel.edit')->middleware('PermissionCheck:manage_parcel');
        Route::post('parcel-update', [ParcelController::class, 'update'])->name('merchant.staff.parcel.update')->middleware('PermissionCheck:manage_parcel');
        Route::post('parcel-delete', [ParcelController::class, 'parcelDelete'])->name('merchant.staff.parcel-delete')->middleware('PermissionCheck:manage_parcel');
        Route::post('parcel-cancel', [ParcelController::class, 'parcelCancel'])->name('merchant.staff.parcel-cancel')->middleware('PermissionCheck:manage_parcel');
        Route::post('parcel-re-request', [ParcelController::class, 'parcelReRequest'])->name('merchant.staff.parcel.re-request')->middleware('PermissionCheck:manage_parcel');
        Route::get('parcel-filtering/{slug}', [ParcelController::class, 'parcelFiltering'])->name('merchant.staff.parcel.filtering')->middleware('PermissionCheck:manage_parcel');
        Route::any('parcel-filter', [ParcelController::class, 'filter'])->name('merchant.staff.parcel.filter')->middleware('PermissionCheck:manage_parcel');
        Route::post('parcel/download', [ParcelController::class, 'getParcelDownload'])->name('merchant-staff.parcel.download');

        Route::get('withdraws', [WithdrawController::class, 'index'])->name('merchant.staff.withdraw')->middleware('PermissionCheck:manage_payment');
        Route::get('request-withdraw', [WithdrawController::class, 'create'])->name('merchant.staff.withdraw.create')->middleware('PermissionCheck:manage_payment');
        Route::post('request-withdraw', [WithdrawController::class, 'store'])->name('merchant.staff.withdraw.store')->middleware('PermissionCheck:manage_payment');
        Route::get('edit-withdraw-request/{id}', [WithdrawController::class, 'edit'])->name('merchant.staff.withdraw.edit')->middleware('PermissionCheck:manage_payment');
        Route::post('update-withdraw-request', [WithdrawController::class, 'update'])->name('merchant.staff.withdraw.update')->middleware('PermissionCheck:manage_payment');
        Route::get('payment-invoice/{id}', [WithdrawController::class, 'invoice'])->name('merchant.staff.invoice')->middleware('PermissionCheck:manage_payment');
        Route::get('payment-invoice-print/{id}', [WithdrawController::class, 'invoicePrint'])->name('merchant.staff.invoice.print')->middleware('PermissionCheck:manage_payment');
        Route::get('withdraw-status/{id}/{status}', [WithdrawController::class, 'changeStatus'])->middleware('PermissionCheck:manage_payment');
        Route::get('shop', [ProfileController::class, 'shop'])->name('merchant.staff.shop');
        Route::get('download-sample', [ImportExportController::class, 'export'])->name('merchant.staff.export');
        Route::get('import', [ImportExportController::class, 'importExportView'])->name('merchant.staff.import.csv');
        Route::post('import', [ImportExportController::class, 'import'])->name('merchant.staff.import');
    });
});
});
