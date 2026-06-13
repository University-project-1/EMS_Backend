<?php


use App\Http\Controllers\Api\V1\Shared\FCMController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\AuthController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ProfileController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function(){
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLink']);
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);
    Route::post('change-password', [ResetPasswordController::class, 'changePassword'])->middleware('auth:system');
});


Route::prefix('admin')->middleware('auth:system')->group(function(){
    // store fcm token
    Route::post('fcm/register-token', [FCMController::class, 'store'])
        ->defaults('guardName', 'web')->name('admin.fcm.store');
        
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile', [ProfileController::class, 'update']);
});
