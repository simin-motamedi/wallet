<?php

namespace App\Services\Sms;
use Illuminate\Support\Facades\Cache;

class OtpSms implements SmsMessage
{
    protected ?int $otpCode;
    protected string $cacheKey;

    public function __construct(private string $mobileNumber)
    {
        $this->cacheKey = 'kavenegar' . $mobileNumber;

        $this->otpCode = rand(10001, 99999);
        Cache::put($this->cacheKey, $this->otpCode, 120);
    }

    /**
     * @return array
     */
    public function payLoad(): array
    {
        return [
            'receptor' => $this->mobileNumber,
            'token' => $this->otpCode,
            'template' => config('sms.template.otp'),
        ];
    }
}
