<?php

use App\Http\Controllers\Api\V1\Mobile\AuthController;
use App\Http\Controllers\Api\V1\Mobile\BoothController;
use App\Http\Controllers\Api\V1\Mobile\PasswordController;
use App\Http\Controllers\Api\V1\Mobile\ProfileController;
use App\Http\Controllers\Api\V1\Shared\FCMController;
use App\Http\Controllers\Api\V1\Shared\HallController;
use Illuminate\Support\Facades\Route;

// auth routes with rate limiting
Route::prefix('auth')->group(function () {

  // login & register routes with rate limiting
  Route::middleware('throttle:login_register')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
  });

  // OTP verification route with its own rate limiting
  Route::middleware('throttle:verify_otp')->group(function () {
    Route::post('register/verify', [AuthController::class, 'verifyRegister']);
    Route::post('otp/resend', [AuthController::class, 'resendOtp']);
  });

  // password reset routes with appropriate rate limiting
  Route::prefix('password')->group(function () {
    Route::post('forgot', [PasswordController::class, 'forgotPassword'])->middleware('throttle:forgot_password');
    Route::post('otp/verify', [PasswordController::class, 'verifyForgotPasswordOtp'])->middleware('throttle:verify_otp');
    Route::post('reset', [PasswordController::class, 'resetPassword'])->middleware('throttle:login_register');
  });
});

// protected routes
Route::prefix('visitor')->middleware('auth:mobile')->group(function(){
  // logout
  Route::delete('auth/logout', [AuthController::class, 'logout']);
  // store fcm token
  Route::post('fcm/register-token', [FCMController::class, 'store'])
    ->defaults('guardName', 'mobile')->name('visitor.fcm.store');

    // profile
  Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show']);
    Route::post('/update', [ProfileController::class, 'updateProfile'])->middleware('throttle:profile_update');
    Route::put('/password/update', [ProfileController::class, 'updatePassword'])->middleware('throttle:password_update');
    Route::post('/phone/request', [ProfileController::class, 'requestPhoneUpdate']);
    Route::post('/phone/verify', [ProfileController::class, 'verifyPhoneUpdate'])->middleware('throttle:verify_otp');

    Route::prefix('booth')->group(function(){
        Route::get('/', [BoothController::class, 'index']);
        Route::get('/{booth}', [BoothController::class, 'show']);
        });

    Route::prefix('halls')->group(function(){
        Route::get('/', [HallController::class, 'index']);
        Route::get('/{hall}', [HallController::class, 'show']);
    });
  });
});
