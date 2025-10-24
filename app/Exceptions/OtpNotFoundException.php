<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use Exception;

class OtpNotFoundException extends CodedException
{
    public function __construct()
    {
        parent::__construct(
            'Otp not found.',
            ErrorCodes::OTP_NOT_FOUND,
            422,
            'otp_not_found'
        );
    }
}
