<?php

use App\Http\Controllers\Api\V1\Shared\FaciltyController;
use App\Http\Controllers\Api\V1\Shared\FCMController;
use App\Http\Controllers\Api\V1\Shared\HallController;
use App\Http\Controllers\Api\V1\Shared\NotificationController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\AnnouncementController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\AuthController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\BoothController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\BoothRequestController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\BusController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\CompanyDirectoryController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\DashboardController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\EventHallController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\EventRequestController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\ManagerDirectoryController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\ReportController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\ServiceController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\VisitorController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ProfileController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:system_login')->name('login');
    Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLink'])->middleware('throttle:forgot_password');
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);

    Route::middleware(['auth:system', 'type.admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        // store fcm token
        Route::post('fcm/register-token', [FCMController::class, 'store'])
            ->defaults('guardName', 'system')->name('admin.fcm.store');

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

        // booths
        Route::prefix('booths')->group(function () {

            Route::prefix('requests')->group(function () {
                Route::get('/', [BoothRequestController::class, 'index']);
                Route::get('/stats', [BoothRequestController::class, 'statistics']);
                Route::get('/{boothRequest}', [BoothRequestController::class, 'show']);
                Route::post('/approve/{boothRequest}', [BoothRequestController::class, 'approve']);
                Route::patch('/reject/{boothRequest}', [BoothRequestController::class, 'reject']);
            });

            Route::patch('/{booth}/cancel', [BoothController::class, 'cancelBooking']);
            Route::get('/', [BoothController::class, 'index']);
            Route::get('/{booth}', [BoothController::class, 'show']);
            Route::patch('/{booth}', [BoothController::class, 'update']);
        });

        // companies
        Route::prefix('companies')->group(function () {
            Route::get('/', [CompanyDirectoryController::class, 'index']);
            Route::get('/{company}', [CompanyDirectoryController::class, 'show']);
        });

        // managers
        Route::prefix('managers')->group(function () {
            Route::get('/directory', [ManagerDirectoryController::class, 'directory']);
            Route::get('/', [ManagerDirectoryController::class, 'index']);
            Route::get('/{manager}', [ManagerDirectoryController::class, 'show']);
        });

        // services
        Route::apiResource('service', ServiceController::class)
            ->only(['index', 'show', 'store', 'update']);

        // announcments
        Route::prefix('announcements')->group(function () {
            Route::get('/', [AnnouncementController::class, 'index']);
            Route::get('/{announcement}', [AnnouncementController::class, 'show']);
            Route::post('/', [AnnouncementController::class, 'store']);
            Route::patch('/{announcement}', [AnnouncementController::class, 'update']);
            Route::delete('/{announcement}', [AnnouncementController::class, 'destroy']);
        });

        // busCatalog
        Route::prefix('buses')->group(function () {
            Route::get('', [BusController::class, 'index']);
            Route::get('/{busCatalog}', [BusController::class, 'show']);
            Route::post('/', [BusController::class, 'create']);
            Route::patch('/{busCatalog}', [BusController::class, 'update']);
            Route::delete('/{busCatalog}', [BusController::class, 'destroy']);
        });

        // facilities
        Route::prefix('facilities')->group(function () {
            Route::get('', [FaciltyController::class, 'index']);
            Route::get('/{facility}', [FaciltyController::class, 'show']);
        });

        // eventHall
        Route::prefix('eventHall/')->group(function () {
            Route::get('', [EventHallController::class, 'index'])->name('admin.event_halls.index');
            Route::get('{eventHall}', [EventHallController::class, 'show'])->name('admin.event_halls.show');
            Route::patch('{eventHall}', [EventHallController::class, 'update']);
        });

        // event request
        Route::prefix('events/requests')->group(function () {
            Route::get('', [EventRequestController::class, 'index'])->name('admin.event_requests.index');
            Route::get('/stats', [EventRequestController::class, 'statistics']);
            Route::get('{event}', [EventRequestController::class, 'show'])->name('admin.event_requests.show');
            Route::post('{event}/approve', [EventRequestController::class, 'approve'])->name('admin.event_requests.approve');
            Route::patch('{event}/reject', [EventRequestController::class, 'reject'])->name('admin.event_requests.reject');
        });

        // visitor
        Route::prefix('visitor/')->group(function () {
            Route::get('', [VisitorController::class, 'index']);
            Route::get('stats', [VisitorController::class, 'statistics']);
        });

        // reports
        Route::prefix('reports')->group(function () {
            Route::get('/statistics', [ReportController::class, 'statistics']);
            Route::get('{report}', [ReportController::class, 'show']);
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/{report}/resolved', [ReportController::class, 'resolved']);
            Route::post('/{report}/rejected', [ReportController::class, 'rejected']);
        });

        // notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('admin.notifications.index')->defaults('guardName', 'system');
            Route::get('/unread', [NotificationController::class, 'unreadNotification'])->name('admin.notifications.unread')->defaults('guardName', 'system');
            Route::get('/unread/count', [NotificationController::class, 'numberOfUnreadNotifications'])->name('admin.notifications.unread.count')->defaults('guardName', 'system');
            Route::get('/statistics', [NotificationController::class, 'statistics'])->name('admin.notifications.statistics')->defaults('guardName', 'system');
            Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.read-all')->defaults('guardName', 'system');
            Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read')->defaults('guardName', 'system');
            Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy')->defaults('guardName', 'system');
        });
    });
});
