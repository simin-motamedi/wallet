<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;

class WalletBalanceIsNotSufficientException extends CodedException
{
    public function __construct()
    {
        parent::__construct(
            'The wallet balance is not sufficient.',
            ErrorCodes::WALLET_BALANCE_IS_NOT_SUFFICIENT,
            422,
            'wallet_balance_is_not_sufficient'
        );
    }
}
