<?php


use App\Http\Controllers\Api\V1\Shared\FCMController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\AuthController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ProfileController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('exhibitor')->group(function(){
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('/auth/system/google', [AuthController::class, 'googleAuth']);
    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');

    Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLink']);
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);
    Route::post('change-password', [ResetPasswordController::class, 'changePassword'])->middleware('auth:system');
});

Route::prefix('exhibitor')->middleware('auth:system')->group(function(){
    // store fcm token
    Route::post('fcm/register-token', [FCMController::class, 'store'])
        ->defaults('guardName', 'web')->name('exhibitor.fcm.store');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile', [ProfileController::class, 'update']);
});
