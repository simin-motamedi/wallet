<?php

namespace App\Services\InvoiceValidators;

use App\Exceptions\InvoiceHasNoTransactionException;
use App\Models\Invoice;
use Exception;

class InvoiceHasTransactionValidator extends InvoiceValidator
{
    /**
     * @throws Exception
     */
    public function check(Invoice $invoice): true
    {
        if (!$invoice->transaction) {
            throw new InvoiceHasNoTransactionException();
        }
        return parent::check($invoice);
    }
}
