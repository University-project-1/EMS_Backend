<?php

use App\Http\Controllers\Api\V1\Mobile\AnnouncementController;
use App\Http\Controllers\Api\V1\Mobile\AuthController;
use App\Http\Controllers\Api\V1\Mobile\BoothController;
use App\Http\Controllers\Api\V1\Mobile\BusCatalogController;
use App\Http\Controllers\Api\V1\Mobile\CompanyController;
use App\Http\Controllers\Api\V1\Mobile\EventController;
use App\Http\Controllers\Api\V1\Mobile\EventHallController;
use App\Http\Controllers\Api\V1\Mobile\LeadController;
use App\Http\Controllers\Api\V1\Mobile\PasswordController;
use App\Http\Controllers\Api\V1\Mobile\ProfileController;
use App\Http\Controllers\Api\V1\Mobile\ReportController;
use App\Http\Controllers\Api\V1\Mobile\ReviewController;
use App\Http\Controllers\Api\V1\Mobile\SavedController;
use App\Http\Controllers\Api\V1\Shared\FaciltyController;
use App\Http\Controllers\Api\V1\Shared\FCMController;
use App\Http\Controllers\Api\V1\Shared\HallController;
use App\Http\Controllers\Api\V1\Shared\NotificationController;
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
Route::prefix('visitor')->middleware('auth:mobile')->group(function () {
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
    });

    // announcments
    Route::get('announcements', [AnnouncementController::class, 'index']);

    // booths
    Route::prefix('booth/')->group(function () {
        Route::get('', [BoothController::class, 'index']);
        Route::get('{booth}', [BoothController::class, 'show']);
    });

    // companies
    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::get('/{company}', [CompanyController::class, 'show']);
    });

    // eventHall
    Route::prefix('eventHall/')->group(function () {
        Route::get('', [EventHallController::class, 'index'])->name('visitor.event_halls.index');
        Route::get('{eventHall}', [EventHallController::class, 'show'])->name('visitor.event_halls.show');
    });

    // events
    Route::prefix('events')->group(function () {
        Route::get('', [EventController::class, 'index'])->name('visitor.events.index');
        Route::get('nearest', [EventController::class, 'nearest'])->name('visitor.events.nearest');
        Route::get('{event}', [EventController::class, 'show'])->name('visitor.events.show');
    });

    // halls
    Route::prefix('halls')->group(function () {
        Route::get('/', [HallController::class, 'index']);
        Route::get('/{hall}', [HallController::class, 'show']);
    });

    // facilities
    Route::prefix('facilities')->group(function(){
        Route::get('', [FaciltyController::class, 'index']);
        Route::get('/{facility}', [FaciltyController::class, 'show']);
    });

    // saved
    Route::prefix('saved')->group(function () {
        Route::post('events/{event}', [SavedController::class, 'toggleEvent']);
        Route::post('booths/{booth}', [SavedController::class, 'toggleBooth']);
        Route::get('booths', [SavedController::class, 'savedBooths']);
    });

    // reports
    Route::post('report', [ReportController::class, 'store'])->middleware('throttle:report');

    // reviews
    Route::prefix('reviews/')->group(function () {
        Route::post('', [ReviewController::class, 'store']);
        Route::get('booth/{booth}', [ReviewController::class, 'boothReviews']);
        Route::get('event/{event}', [ReviewController::class, 'eventReviews']);
        Route::delete('{review}', [ReviewController::class, 'destroy']);
    });

    // leads
    Route::prefix('leads')->group(function(){
        Route::post('/', [LeadController::class, 'store']);
        Route::get('/history', [LeadController::class, 'index']);
    });
    // notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('visitor.notifications.index')->defaults('guardName', 'mobile');
        Route::get('/unread', [NotificationController::class, 'unreadNotification'])->name('visitor.notifications.unread')->defaults('guardName', 'mobile');
        Route::get('/unread/count', [NotificationController::class, 'numberOfUnreadNotifications'])->name('visitor.notifications.unread.count')->defaults('guardName', 'mobile');
        Route::get('/statistics', [NotificationController::class, 'statistics'])->name('visitor.notifications.statistics')->defaults('guardName', 'mobile');
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('visitor.notifications.read-all')->defaults('guardName', 'mobile');
        Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('visitor.notifications.read')->defaults('guardName', 'mobile');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('visitor.notifications.destroy')->defaults('guardName', 'mobile');
    });

    // bus catalog
    Route::get('bus-catalog', [BusCatalogController::class, 'index'])->name('visitor.bus_catalog.index');

});
