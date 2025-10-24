<?php

namespace App\Services\InvoiceValidators;

use App\Exceptions\WalletIsNotActiveException;
use App\Models\Invoice;
use App\Enums\WalletStatus;
use Exception;

class WalletIsActiveValidator extends InvoiceValidator
{
    /**
     * @throws Exception
     */
    public function check(Invoice $invoice): true
    {
        if ($invoice->wallet->status !== WalletStatus::ACTIVE->value) {
            throw new WalletIsNotActiveException();
        }
        return parent::check($invoice);
    }
}
