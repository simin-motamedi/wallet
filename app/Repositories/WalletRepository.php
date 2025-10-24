<?php

namespace App\Repositories;

use App\Enums\InvoiceState;
use App\Enums\TransactionState;
use App\Models\Invoice;
use Exception;
use Illuminate\Support\Facades\DB;

class WalletRepository
{
    /**
     * @param Invoice $invoice
     * @return void
     * @throws Exception
     */
    public function purchase(Invoice $invoice): void
    {
        try {
            DB::beginTransaction();

            $wallet = $invoice->wallet()->lockForUpdate()->first();
            $newBalance = $wallet->balance - $invoice->transaction->amount;

            $invoice->update([
                'paid_at' => now(),
                'state' => InvoiceState::APPROVED->value,
            ]);

            $invoice->transaction->update([
                'state' => TransactionState::APPROVED->value,
            ]);


            $wallet->update([
                'balance' => $newBalance
            ]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
