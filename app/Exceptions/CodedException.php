<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use Exception;

abstract class CodedException extends Exception
{
    protected ErrorCodes $errorCode;
    protected string $errorType;

    public function __construct(string $message, ErrorCodes $errorCode, int $httpStatus = 400, string $errorType = 'error')
    {
        parent::__construct($message, $httpStatus);
        $this->errorCode = $errorCode;
        $this->errorType = $errorType;
    }

    public function getErrorCode(): ErrorCodes
    {
        return $this->errorCode;
    }

    public function getErrorType(): string
    {
        return $this->errorType;
    }
}
