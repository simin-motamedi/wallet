<?php

namespace App\Services\InvoiceValidators;

use App\Enums\InvoiceState;
use App\Exceptions\InvoiceAlreadyPaidException;
use App\Models\Invoice;
use Exception;

class InvoiceIsNotPaidValidator extends InvoiceValidator
{
    /**
     * @throws Exception
     */
    public function check(Invoice $invoice): true
    {
        if ($invoice->state == InvoiceState::APPROVED->value) {
            throw new InvoiceAlreadyPaidException();
        }
        return parent::check($invoice);
    }
}
