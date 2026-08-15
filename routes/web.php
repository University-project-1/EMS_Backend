<?php

use App\Http\Controllers\Web\v1\Mobile\ScanController;
use App\Http\Controllers\Web\VolunteerApplicationController;
use App\Http\Middleware\SetVolunteerLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/scan/{token}', [ScanController::class, 'show'])->name('scan');

Route::middleware(SetVolunteerLocale::class)->prefix('volunteer')->name('volunteer.application.')->group(function (): void {
    Route::get('apply', [VolunteerApplicationController::class, 'create'])->name('create');
    Route::get('locale/{locale}', [VolunteerApplicationController::class, 'changeLocale'])
        ->name('locale');
    Route::post('apply', [VolunteerApplicationController::class, 'store'])
        ->middleware('throttle:volunteer-application')
        ->name('store');
    Route::get('application-received', [VolunteerApplicationController::class, 'received'])->name('received');
});
