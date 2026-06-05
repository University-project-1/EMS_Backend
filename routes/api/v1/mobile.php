<?php

use App\Http\Controllers\Api\V1\Mobile\AuthController;
use App\Http\Controllers\Api\V1\Shared\FCMController;
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
    Route::post('forgot', [AuthController::class, 'forgotPassword'])->middleware('throttle:forgot_password');
    Route::post('otp/verify', [AuthController::class, 'verifyForgotPasswordOtp'])->middleware('throttle:verify_otp');
    Route::post('reset', [AuthController::class, 'resetPassword'])->middleware('throttle:login_register');
  });
});

// protected routes
Route::prefix('visitor')->middleware('auth:mobile')->group(function(){
  // logout
  Route::delete('auth/logout', [AuthController::class, 'logout']);
  // store fcm token 
  Route::post('fcm/register-token', [FCMController::class, 'store'])
    ->defaults('guardName', 'mobile')->name('visitor.fcm.store');
});
