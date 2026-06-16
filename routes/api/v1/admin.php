<?php


use App\Http\Controllers\Api\V1\Shared\FCMController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\AuthController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ProfileController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function(){
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLink'])->name('admin.sendResetLink')->middleware('throttle:password_update');
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword'])->name('admin.resetPassword');
});


Route::prefix('admin')->middleware('auth:system')->group(function(){
    // store fcm token
    Route::post('fcm/register-token', [FCMController::class, 'store'])
        ->defaults('guardName', 'web')->name('admin.fcm.store');

    Route::post('change-password', [ResetPasswordController::class, 'changePassword'])->name('admin.change-password')->middleware('throttle:password_update');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('profile', [ProfileController::class, 'show'])->name('admin.profile.show');
    Route::post('profile', [ProfileController::class, 'update'])->name('admin.profile.update')->middleware('throttle:profile_update');
});
