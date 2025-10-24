<?php

namespace App\Services\InvoiceValidators;

use App\Exceptions\UserIsNotActiveException;
use App\Models\Invoice;
use App\Enums\UserStatus;
use Exception;

class UserIsActiveValidator extends InvoiceValidator
{
    /**
     * @throws Exception
     */
    public function check(Invoice $invoice): true
    {
        if ($invoice->transaction->user->status !== UserStatus::ACTIVE->value) {
            throw new UserIsNotActiveException();
        }
        return parent::check($invoice);
    }
}
