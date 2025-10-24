<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use Exception;

class InvoiceIsExpiredException extends CodedException
{
    public function __construct()
    {
        parent::__construct(
            'The invoice is expired.',
            ErrorCodes::INVOICE_IS_EXPIRED,
            422,
            'invoice_is_expired'
        );
    }
}
