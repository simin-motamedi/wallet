<?php


namespace App\Services\Sms;
class InvoicePaidSms implements SmsMessage
{
    public function __construct(
        private string $mobile, private string $invoiceUuid
    )
    {
    }

    /**
     * @return array
     */
    public function payLoad(): array
    {

        return [
            'receptor' => $this->mobile,
            'template' => config('sms.template.complete'),
            'token' => $this->invoiceUuid,
        ];
    }

}
