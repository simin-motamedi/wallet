<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use Exception;

class InvalidOtpException extends CodedException
{
    public function __construct()
    {
        parent::__construct(
            'Invalid OTP',
            ErrorCodes::INVALID_OTP,
            422,
            'invalid_otp'
        );
    }
}
