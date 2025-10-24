<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use Exception;

class InvoiceHasNoTransactionException extends CodedException
{
    public function __construct()
    {
        parent::__construct(
            'The invoice has no transaction.',
            ErrorCodes::INVOICE_HAS_NO_TRANSACTION,
            422,
            'invoice_has_no_transaction'
        );
    }
}
