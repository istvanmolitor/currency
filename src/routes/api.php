<?php

use Illuminate\Support\Facades\Route;
use Molitor\Currency\Http\Controllers\CurrencyController;

// Admin routes
Route::prefix('admin/currency')
    ->middleware(['api', 'auth:sanctum'])
    ->name('currency.')
    ->group(function () {
        Route::resource('currencies', CurrencyController::class);
    });
