<?php

namespace App\Services\InvoiceValidators;

use App\Exceptions\InvoiceIsExpiredException;
use App\Models\Invoice;
use Exception;

class IsNotExpiredValidator extends InvoiceValidator
{
    /**
     * @throws Exception
     */
    public function check(Invoice $invoice): true
    {
        if ($invoice->isExpired()) {
            throw new InvoiceIsExpiredException();
        }
        return parent::check($invoice);
    }
}
