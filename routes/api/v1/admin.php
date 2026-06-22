<?php

use App\Http\Controllers\Api\V1\Shared\EventHallController;
use App\Http\Controllers\Api\V1\Shared\FCMController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\AuthController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\BoothController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\EventHallController as AdminEventHallController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\ServiceController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ProfileController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLink'])->middleware('throttle:forgot_password');
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);


    Route::middleware(['auth:system', 'type.admin'])->group(function () {
        // store fcm token
        Route::post('fcm/register-token', [FCMController::class, 'store'])
            ->defaults('guardName', 'web')->name('admin.fcm.store');

        Route::post('change-password', [ResetPasswordController::class, 'changePassword'])->middleware('throttle:password_update');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('profile', [ProfileController::class, 'show']);
        Route::post('profile', [ProfileController::class, 'update']);

        Route::prefix('booths')->group(function(){
            Route::get('/', [BoothController::class, 'index']);
            Route::get('/{booth}', [BoothController::class, 'show']);
            Route::patch('/{booth}', [BoothController::class, 'update']);
        });

        Route::resource('service', ServiceController::class);

        // eventHall
        Route::prefix('eventHall/')->group(function(){
            Route::get('', [EventHallController::class, 'index'])->name('admin.event_halls.index');
            Route::get('{eventHall}', [EventHallController::class, 'show'])->name('admin.event_halls.show');
            Route::patch('{eventHall}', [AdminEventHallController::class, 'update']);
        });
    });
});
