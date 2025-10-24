<?php

namespace App\Services\InvoiceValidators;

use App\Exceptions\WalletBalanceIsNotSufficientException;
use App\Models\Invoice;
use Exception;

class WalletHasSufficientBalanceValidator extends InvoiceValidator
{
    /**
     * @throws Exception
     */
    public function check(Invoice $invoice): true
    {
        if ($invoice->wallet->balance < $invoice->transaction->amount) {
            throw new WalletBalanceIsNotSufficientException();
        }
        return parent::check($invoice);
    }
}
