<?php

use App\Http\Controllers\Api\V1\Shared\FCMController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('visitor')->middleware('auth:mobile')->group(function(){
    // store fcm token 
    Route::post('fcm/register-token', [FCMController::class, 'store'])
        ->defaults('guardName', 'mobile')->name('visitor.fcm.store');
});
