<?php

use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    require __DIR__ . '/api/v1/admin.php';
    require __DIR__ . '/api/v1/exhibitor.php';
    require __DIR__ . '/api/v1/mobile.php';
});
