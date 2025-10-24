<?php

namespace App\Enums;

enum ErrorCodes: int
{
    case INVOICE_ALREADY_PAID = 10001;
    case INVOICE_IS_EXPIRED = 1002;
    case INVOICE_HAS_NO_TRANSACTION = 1003;
    case USER_IS_NOT_ACTIVE = 1004;
    case WALLET_BALANCE_IS_NOT_SUFFICIENT = 1005;
    case WALLET_IS_NOT_ACTIVE = 1006;
    case INVOICE_HAS_NO_WALLET = 1007;
    case OTP_NOT_FOUND = 1008;
    case INVALID_OTP = 1009;
}
