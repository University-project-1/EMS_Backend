<?php

use App\Http\Controllers\Web\v1\Mobile\ScanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/scan/{token}', [ScanController::class, 'show'])->name('scan');
