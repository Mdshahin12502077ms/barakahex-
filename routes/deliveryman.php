<?php

use App\Http\Controllers\DeliveryMan\AccountController;
use App\Http\Controllers\DeliveryMan\DashboardController;
use App\Http\Controllers\DeliveryMan\ParcelController;
use App\Http\Controllers\DeliveryMan\ProfileController;
use App\Http\Controllers\DeliveryMan\ReturnTaskController;
use Illuminate\Support\Facades\Route;


Route::prefix('delivery-man')->middleware(['LoginCheckDeliveryMan'])->name('deliveryman.')->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Pickups
    Route::get('/pickups', [ParcelController::class, 'pickups'])->name('pickups');
    Route::post('/pickup-received/{id}', [ParcelController::class, 'pickupReceived'])->name('pickup.received');
    Route::post('/pickup-reschedule/{id}', [ParcelController::class, 'reschedulePickup'])->name('pickup.reschedule');
    Route::post('/pickup-cancel/{id}', [ParcelController::class, 'cancelPickup'])->name('pickup.cancel');

    // 3. Deliveries
    Route::get('/deliveries', [ParcelController::class, 'deliveries'])->name('deliveries');
    Route::get('/parcel-detail/{id}', [ParcelController::class, 'parcelDetail'])->name('parcel.detail');
    Route::post('/delivered/{id}', [ParcelController::class, 'delivered'])->name('delivered');
    Route::post('/verify-otp/{id}', [ParcelController::class, 'verifyOtp'])->name('verify.otp');
    Route::post('/resend-otp/{id}', [ParcelController::class, 'resendOtp'])->name('resend.otp');
    Route::post('/reschedule/{id}', [ParcelController::class, 'reschedule'])->name('reschedule');
    Route::post('/cancel/{id}', [ParcelController::class, 'cancel'])->name('cancel');
     //Return Delivery Task

     Route::controller(ReturnTaskController::class)->group(function(){
        Route::get('/return-tasks', 'index')->name('return.tasks');
        Route::post('/return-task-complete/{id}', 'complete')->name('return.task.complete');
     });




    // 4. Accounts
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts');

    // 5. Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile-update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change.password');
});
