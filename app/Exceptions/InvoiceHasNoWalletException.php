<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use Exception;

class InvoiceHasNoWalletException extends CodedException
{
    public function __construct()
    {
        parent::__construct(
            'The invoice has no wallet.',
            ErrorCodes::INVOICE_HAS_NO_WALLET,
            422,
            'invoice_has_no_wallet'
        );
    }
}
