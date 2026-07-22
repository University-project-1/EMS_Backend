<?php

use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\AnnouncementController;
use App\Http\Controllers\Api\V1\Shared\EventHallController;
use App\Http\Controllers\Api\V1\Shared\FCMController;
use App\Http\Controllers\Api\V1\Shared\HallController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\AuthController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\BoothController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\CompanyController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\EventController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\InvitationController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\ServiceController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ProfileController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('exhibitor')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('/email/resend-verification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware(['auth:system', 'throttle:verify_otp']);
    Route::post('/auth/system/google', [AuthController::class, 'googleAuth']);
    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
    Route::get('/auth/status', [AuthController::class, 'checkStatus'])->middleware('auth:system');

    Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLink'])->middleware('throttle:forgot_password');
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);

    Route::middleware(['auth:system', 'verified'])->group(function () {
        // store fcm token
        Route::post('fcm/register-token', [FCMController::class, 'store'])
            ->defaults('guardName', 'web')->name('exhibitor.fcm.store');

        Route::post('change-password', [ResetPasswordController::class, 'changePassword'])->middleware('throttle:password_update');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('profile', [ProfileController::class, 'show']);
        Route::post('profile', [ProfileController::class, 'update']);

        Route::prefix('halls')->group(function () {
            Route::get('/', [HallController::class, 'index']);
            Route::get('/{hall}', [HallController::class, 'show']);
        });

        Route::prefix('booth')->group(function () {
            Route::get('/', [BoothController::class, 'index']);
            Route::get('/my', [BoothController::class, 'ownedBooths']);
            Route::post('request-booth', [BoothController::class, 'book']);
            Route::get('/{booth}', [BoothController::class, 'show']);
            Route::get('/{booth}/invitations', [InvitationController::class, 'boothInvitations']);
            Route::post('/{booth}/invitations', [InvitationController::class, 'storeForBooth']);
        });

        Route::prefix('companies')->group(function () {
            Route::get('/{company}/profile', [CompanyController::class, 'show']);
            Route::get('/{company}/invitations', [InvitationController::class, 'companyInvitations']);
            Route::post('/{company}/invitations', [InvitationController::class, 'storeForCompany']);
        });

        Route::prefix('invitation')->group(function () {
            Route::get('/{token}', [InvitationController::class, 'show']);
            Route::post('/{token}/accept', [InvitationController::class, 'approve']);
            Route::post('/{token}/reject', [InvitationController::class, 'reject']);
            Route::delete('/{invitation}', [InvitationController::class, 'delete']);
        });

        Route::get('announcements', [AnnouncementController::class, 'index']);
        Route::get('services', [ServiceController::class, 'index']);

        // eventHall
        Route::prefix('eventHall/')->group(function () {
            Route::get('', [EventHallController::class, 'index'])->name('exhibitor.event_halls.index');
            Route::get('{eventHall}', [EventHallController::class, 'show'])->name('exhibitor.event_halls.show');
        });

        // events
        Route::prefix('events/')->group(function () {
            Route::get('calendar', [EventController::class, 'calendar'])->name('exhibitor.events.calendar');
            Route::get('nearest', [EventController::class, 'nearest'])->name('exhibitor.events.nearest');
            Route::get('statistics', [EventController::class, 'statistics'])->name('exhibitor.events.statistics');
            Route::get('', [EventController::class, 'index'])->name('exhibitor.events.index');
            Route::post('', [EventController::class, 'store']);
        });
    });
});
