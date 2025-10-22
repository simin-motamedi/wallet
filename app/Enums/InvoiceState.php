<?php

namespace App\Enums;

enum InvoiceState: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case EXPIRED = 'expired';
    case REJECTED = 'rejected';
}
