<?php

namespace App\Services\InvoiceValidators;

use App\Exceptions\InvoiceHasNoWalletException;
use App\Models\Invoice;
use Exception;

class InvoiceHasWalletValidator extends InvoiceValidator
{
    /**
     * @throws Exception
     */
    public function check(Invoice $invoice): true
    {
        if (!$invoice->wallet) {
            throw new InvoiceHasNoWalletException();
        }
        return parent::check($invoice);
    }
}
