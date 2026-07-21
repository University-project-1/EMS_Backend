<?php

use App\Http\Controllers\Api\V1\Shared\EventHallController;
use App\Http\Controllers\Api\V1\Shared\FCMController;
use App\Http\Controllers\Api\V1\Shared\HallController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\AuthController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\BoothController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\BoothRequestController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\CompanyDirectoryController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\EventHallController as AdminEventHallController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\EventRequestController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\ManagerDirectoryController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\ServiceController;
use App\Http\Controllers\Api\V1\SystemUser\Admin\VisitorController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ProfileController;
use App\Http\Controllers\Api\V1\SystemUser\Shared\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLink'])->middleware('throttle:forgot_password');
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);

    Route::middleware(['auth:system', 'type.admin'])->group(function () {
        // store fcm token
        Route::post('fcm/register-token', [FCMController::class, 'store'])
            ->defaults('guardName', 'web')->name('admin.fcm.store');

        Route::post('change-password', [ResetPasswordController::class, 'changePassword'])->middleware('throttle:password_update');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('profile', [ProfileController::class, 'show']);
        Route::post('profile', [ProfileController::class, 'update']);

        Route::prefix('halls')->group(function () {
            Route::get('/', [HallController::class, 'index']);
            Route::get('/{hall}', [HallController::class, 'show']);
        });

        Route::prefix('booths')->group(function () {

            Route::prefix('requests')->group(function () {
                Route::get('/', [BoothRequestController::class, 'index']);
                Route::get('/stats', [BoothRequestController::class, 'statistics']);
                Route::get('/{boothRequest}', [BoothRequestController::class, 'show']);
                Route::post('/approve/{boothRequest}', [BoothRequestController::class, 'approve']);
                Route::patch('/reject/{boothRequest}', [BoothRequestController::class, 'reject']);
            });

            Route::get('/', [BoothController::class, 'index']);
            Route::get('/{booth}', [BoothController::class, 'show']);
            Route::patch('/{booth}', [BoothController::class, 'update']);
        });

        Route::prefix('companies')->group(function () {
            Route::get('/', [CompanyDirectoryController::class, 'index']);
            Route::get('/{company}', [CompanyDirectoryController::class, 'show']);
        });

        Route::prefix('managers')->group(function () {
            Route::get('/directory', [ManagerDirectoryController::class, 'directory']);
            Route::get('/', [ManagerDirectoryController::class, 'index']);
            Route::get('/{manager}', [ManagerDirectoryController::class, 'show']);
        });

        Route::resource('service', ServiceController::class);

        // eventHall
        Route::prefix('eventHall/')->group(function () {
            Route::get('', [EventHallController::class, 'index'])->name('admin.event_halls.index');
            Route::get('{eventHall}', [EventHallController::class, 'show'])->name('admin.event_halls.show');
            Route::patch('{eventHall}', [AdminEventHallController::class, 'update']);
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
        Route::prefix('visitor/')->group(function(){
            Route::get('', [VisitorController::class, 'index']);
            Route::get('stats', [VisitorController::class, 'statistics']);
        });
    });
});
