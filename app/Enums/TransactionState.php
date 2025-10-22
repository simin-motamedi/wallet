<?php

namespace App\Enums;

enum TransactionState: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case EXPIRED = 'expired';
    case REJECTED = 'rejected';
}
