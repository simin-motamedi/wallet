<?php

namespace App\Services\InvoiceValidators;

use App\Models\Invoice;

abstract class InvoiceValidator
{
    protected ?InvoiceValidator $next = null;

    public function setNext(InvoiceValidator $next): InvoiceValidator
    {
        $this->next = $next;
        return $next;
    }

    public function check(Invoice $invoice): true
    {
        if (!$this->next) {
            return true;
        }
        return $this->next->check($invoice);
    }
}
