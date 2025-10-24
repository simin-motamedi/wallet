<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
class InvoiceAlreadyPaidException extends CodedException
{
    public function __construct()
    {
        parent::__construct(
            'The invoice has already been paid.',
            ErrorCodes::INVOICE_ALREADY_PAID,
            422,
            'invoice_already_paid'
        );
    }
}
