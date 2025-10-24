<?php

namespace App\Http\Middleware;

use App\Enums\InvoiceState;
use App\Models\Invoice;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckDailySpendingLimit
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $limit = config('app.daily_spending_limit', 1000000);
        $cacheKey = 'daily_spent_total_' . now()->toDateString();

        $totalToday = Cache::remember($cacheKey, now()->endOfDay(), function () {
            return Invoice::whereDate('paid_at', today())
                ->where('invoices.state', InvoiceState::APPROVED->value)
                ->join('transactions', function ($join) {
                    $join->on('transactions.transactionable_id', '=', 'invoices.id')
                        ->where('transactions.transactionable_type', '=', Invoice::class);
                })
                ->sum('transactions.amount');
        });



        if ($totalToday >= $limit) {
            return response()->json([
                'error' => 'Daily spending limit reached. Please try again tomorrow.'
            ], 429);
        }

        return $next($request);
    }
}
