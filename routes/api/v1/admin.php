<?php

use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\ResetPasswordController;
use App\Http\Controllers\Api\V1\Shared\FCMController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('auth:system')->group(function(){
    // store fcm token
    Route::post('fcm/register-token', [FCMController::class, 'store'])
        ->defaults('guardName', 'web')->name('admin.fcm.store');
});

Route::prefix('admin')->group(function(){
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLink']);
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);
    Route::post('change-password', [ResetPasswordController::class, 'changePassword'])->middleware('auth:system');
});
