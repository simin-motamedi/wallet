<?php


namespace App\Services\Sms;
class InvoiceFailedSms implements SmsMessage
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
            'template' => config('sms.template.failed'),
            'token' => $this->invoiceUuid,
            'token2' => $this->transaction->user->first_name,
        ];
    }

}
