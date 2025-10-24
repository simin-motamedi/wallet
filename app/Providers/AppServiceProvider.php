<?php

namespace App\Providers;

use App\Http\Controllers\Invoices\InvoicePayController;
use App\Http\Controllers\Invoices\InvoicePrepareController;
use App\Interfaces\TransactionProcessor;
use App\Services\InvoiceTransactionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->when(InvoicePayController::class)
            ->needs(TransactionProcessor::class)
            ->give(InvoiceTransactionService::class);

        $this->app->when(InvoicePrepareController::class)
            ->needs(TransactionProcessor::class)
            ->give(InvoiceTransactionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
