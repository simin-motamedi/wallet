<?php

namespace App\Services\Sms;
interface SmsMessage
{
    public function payLoad(): array;
}
