<?php

use App\Http\Controllers\Invoices\InvoicePayController;
use App\Http\Controllers\Invoices\InvoicePrepareController;
use App\Http\Middleware\CheckDailySpendingLimit;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('invoices')->group(function () {
        Route::middleware(CheckDailySpendingLimit::class)
            ->post('{invoice:uuid}/prepare', InvoicePrepareController::class);
        Route::post('{invoice:uuid}/pay', InvoicePayController::class);
    });
});
