<?php

use Illuminate\Support\Facades\Route;
use Molitor\Currency\Http\Controllers\CurrencyController;

// Admin routes
Route::prefix('admin/currency')
    ->middleware(['api', 'auth:sanctum', 'permission:currency'])
    ->name('currency.')
    ->group(function () {
        Route::get('currencies/select', [CurrencyController::class, 'select']);
        Route::resource('currencies', CurrencyController::class);
    });
