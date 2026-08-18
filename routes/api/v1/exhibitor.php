<?php

use App\Http\Controllers\Api\V1\Shared\FaciltyController;
use App\Http\Controllers\Api\V1\Shared\FCMController;
use App\Http\Controllers\Api\V1\Shared\HallController;
use App\Http\Controllers\Api\V1\Shared\NotificationController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\AnnouncementController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\AuthController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\BoothController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\BoothRequestController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\CompanyController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\EventController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\EventHallController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\InvitationController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\LeadController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\LookupController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\ReviewController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\ServiceController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ProfileController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('exhibitor')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:system_login')->name('login');
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:registration');
    Route::get('invitations/{invitation:token}', [InvitationController::class, 'show']);
    Route::post('register/{invitation:token}', [AuthController::class, 'registerViaInvite']);
    Route::post('/email/resend-verification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware(['auth:system', 'type.exhibitor', 'throttle:verify_otp']);
    Route::post('/auth/system/google', [AuthController::class, 'googleAuth']);
    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
    Route::get('/auth/status', [AuthController::class, 'checkStatus'])
        ->middleware(['auth:system', 'type.exhibitor']);

    Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLink'])->middleware('throttle:forgot_password');
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);

    // nearest events
    Route::get('events/nearest', [EventController::class, 'nearest'])->name('exhibitor.events.nearest');

    Route::middleware(['auth:system', 'type.exhibitor', 'verified'])->group(function () {
        // store fcm token
        Route::post('fcm/register-token', [FCMController::class, 'store'])
            ->defaults('guardName', 'system')->name('exhibitor.fcm.store');

        Route::post('change-password', [ResetPasswordController::class, 'changePassword'])->middleware('throttle:password_update');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // profile
        Route::get('profile', [ProfileController::class, 'show']);
        Route::post('profile', [ProfileController::class, 'update'])->middleware('throttle:profile_update');

        // halls
        Route::prefix('halls')->group(function () {
            Route::get('/', [HallController::class, 'index']);
            Route::get('/{hall}', [HallController::class, 'show']);
        });

        Route::prefix('booth-requests')->group(function () {
            Route::get('/', [BoothRequestController::class, 'index']);
        });

        // booths
        Route::prefix('booth')->group(function () {
            Route::get('/', [BoothController::class, 'index']);
            Route::get('/my', [BoothController::class, 'ownedBooths']);
            Route::post('request-booth', [BoothController::class, 'book'])->middleware('throttle:booth_request');
            Route::get('/{booth}', [BoothController::class, 'show']);
            Route::get('/{booth}/invitations', [InvitationController::class, 'boothInvitations']);
            Route::post('/{booth}/invitations', [InvitationController::class, 'storeForBooth']);
        });

        // companies
        Route::prefix('companies')->group(function () {
            Route::get('/{company}/profile', [CompanyController::class, 'show']);
            Route::get('/{company}/invitations', [InvitationController::class, 'companyInvitations']);
            Route::post('/{company}/invitations', [InvitationController::class, 'storeForCompany']);
        });

        // lookups
        Route::prefix('lookup')->group(function () {
            Route::get('/booths', [LookupController::class, 'booths'])->name('exhibitor.lookup.booths');
            Route::get('/events', [LookupController::class, 'events'])->name('exhibitor.lookup.events');
            Route::get('/companies', [LookupController::class, 'companies'])->name('exhibitor.lookup.companies');
        });

        // invitations
        Route::prefix('invitations')->group(function () {
            Route::post('/{invitation:token}/accept', [InvitationController::class, 'approve']);
            Route::post('/{invitation:token}/reject', [InvitationController::class, 'reject']);
            Route::delete('{invitation}', [InvitationController::class, 'destroy']);
        });

        // announcments
        Route::get('announcements', [AnnouncementController::class, 'index']);
        Route::get('services', [ServiceController::class, 'index']);

        Route::prefix('leads')->group(function () {
            Route::get('/booths/{booth}', [LeadController::class, 'boothLeads']);
            Route::get('/events/{event}', [LeadController::class, 'eventLeads']);
        });

        // eventHall
        Route::prefix('eventHall/')->group(function () {
            Route::get('', [EventHallController::class, 'index'])->name('exhibitor.event_halls.index');
            Route::get('{eventHall}', [EventHallController::class, 'show'])->name('exhibitor.event_halls.show');
        });

        // facilities
        Route::prefix('facilities')->group(function () {
            Route::get('', [FaciltyController::class, 'index']);
            Route::get('/{facility}', [FaciltyController::class, 'show']);
        });

        // events
        Route::prefix('events/')->group(function () {
            Route::get('calendar', [EventController::class, 'calendar'])->name('exhibitor.events.calendar');
            Route::get('statistics', [EventController::class, 'statistics'])->name('exhibitor.events.statistics');
            Route::get('', [EventController::class, 'index'])->name('exhibitor.events.index');
            Route::post('', [EventController::class, 'store'])->middleware('throttle:event_request');
        });

        // notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('exhibitor.notifications.index')->defaults('guardName', 'system');
            Route::get('/unread', [NotificationController::class, 'unreadNotification'])->name('exhibitor.notifications.unread')->defaults('guardName', 'system');
            Route::get('/unread/count', [NotificationController::class, 'numberOfUnreadNotifications'])->name('exhibitor.notifications.unread.count')->defaults('guardName', 'system');
            Route::get('/statistics', [NotificationController::class, 'statistics'])->name('exhibitor.notifications.statistics')->defaults('guardName', 'system');
            Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('exhibitor.notifications.read-all')->defaults('guardName', 'system');
            Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('exhibitor.notifications.read')->defaults('guardName', 'system');
            Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('exhibitor.notifications.destroy')->defaults('guardName', 'system');
        });

        // reviews
        Route::prefix('reviews')->group(function () {
            Route::get('event/{event}', [ReviewController::class, 'eventReviews']);
            Route::get('booht/{booth}', [ReviewController::class, 'boothReviews']);
        });
    });
});
