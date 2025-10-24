<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use Exception;

class WalletIsNotActiveException extends CodedException
{
    public function __construct()
    {
        parent::__construct(
            'The wallet is not active.',
            ErrorCodes::WALLET_IS_NOT_ACTIVE,
            422,
            'wallet_is_not_active'
        );
    }
}
