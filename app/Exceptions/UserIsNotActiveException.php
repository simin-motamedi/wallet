<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;

class UserIsNotActiveException extends CodedException
{
    public function __construct()
    {
        parent::__construct(
            'The user is not active.',
            ErrorCodes::USER_IS_NOT_ACTIVE,
            422,
            'user_is_not_active'
        );
    }
}
