<?php

namespace App\Services;

use App\Enums\InvoiceState;
use App\Enums\TransactionState;
use App\Exceptions\InvalidOtpException;
use App\Exceptions\OtpNotFoundException;
use App\Interfaces\TransactionProcessor;
use App\Jobs\SendSms;
use App\Models\Invoice;
use App\Services\InvoiceValidators\WalletHasSufficientBalanceValidator;
use App\Services\InvoiceValidators\InvoiceHasTransactionValidator;
use App\Services\InvoiceValidators\InvoiceHasWalletValidator;
use App\Services\InvoiceValidators\InvoiceIsNotPaidValidator;
use App\Services\InvoiceValidators\IsNotExpiredValidator;
use App\Services\InvoiceValidators\UserIsActiveValidator;
use App\Services\InvoiceValidators\WalletIsActiveValidator;
use App\Services\Sms\InvoiceFailedSms;
use App\Services\Sms\InvoicePaidSms;
use App\Services\Sms\OtpSms;
use Exception;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceTransactionService implements TransactionProcessor
{
    /**
     * @throws Exception
     */
    public function validate(Invoice|Model $invoice): void
    {
        $validator = new InvoiceIsNotPaidValidator();
        $validator
            ->setNext(new InvoiceHasTransactionValidator())
            ->setNext(new InvoiceHasWalletValidator())
            ->setNext(new IsNotExpiredValidator())
            ->setNext(new WalletHasSufficientBalanceValidator())
            ->setNext(new UserIsActiveValidator())
            ->setNext(new WalletIsActiveValidator());

        $validator->check($invoice);
        $mobile = $invoice->transaction->user->mobile;
        SendSms::dispatch(new OtpSms($mobile));
    }

    /**
     * @param Invoice|Model $invoice
     * @param int $otp
     * @return void
     * @throws Exception
     */
    public function process(Invoice|Model $invoice, int $otp): void
    {
        $this->validate($invoice);

        $this->validateOtp($invoice, $otp);

        try {
            $response = $this->mockPaymentApi();

            if ($response['success']) {
                $this->purchase($invoice);
            } else {
                throw new Exception($response['message']);
            }
        } catch
        (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception($exception->getMessage());
        }

    }

    /**
     * @throws Exception
     */
    private function purchase(Invoice $invoice): void
    {
        try {

            DB::beginTransaction();

            $wallet = $invoice->wallet()->lockForUpdate()->first();
            $newBalance = $wallet->balance - $invoice->transaction->amount;

            $invoice->update([
                'paid_at' => now(),
                'state' => InvoiceState::APPROVED->value,
            ]);

            $invoice->transaction->update([
                'state' => TransactionState::APPROVED->value,
            ]);


            $wallet->update([
                'balance' => $newBalance
            ]);

            DB::commit();

            $this->updateTotal($invoice);

            $this->sendSuccessSms($invoice);

        } catch (Exception $exception) {
            DB::rollBack();
            $this->sendFailedPaymentSms($invoice);
            throw $exception;
        }
    }

    /**
     * @return array
     */
    protected function mockPaymentApi(): array
    {
        $success =  app()->environment('testing') ? true: fake()->randomElement([true, false]);
        return [
            'success' => $success,
            'message' => $success ? 'payment successful' : 'payment failed',
        ];
    }

    /**
     * @param Invoice $invoice
     * @return void
     * @throws LockTimeoutException
     */
    private function updateTotal(Invoice $invoice): void
    {
        $cacheKey = 'daily_spent_total_' . now()->toDateString();
        $amount = $invoice->transaction->amount;

        Cache::lock('daily_spent_total_lock_' . now()->toDateString(), 10)->block(5, function () use ($cacheKey, $amount) {
            if (!Cache::has($cacheKey)) {
                Cache::put($cacheKey, 0, now()->endOfDay());
            }

            Cache::increment($cacheKey, $amount);
        });
    }

    /**
     * @param Invoice $invoice
     * @return void
     */
    private function sendSuccessSms(Invoice $invoice): void
    {
        $mobile = $invoice->transaction->user->mobile;
        SendSms::dispatch(new InvoicePaidSms($mobile, $invoice->transaction->uuid));
    }

    /**
     * @throws Exception
     */
    private function validateOtp(Model|Invoice $invoice, int $otp): void
    {
        if (app()->environment('testing')) {
            return;
        }
        if (config('app.env') !== 'local') {
            $cacheKey = 'kavenegar' . $invoice->transaction->user->mobile;

            $cachedOtp = Cache::get($cacheKey);

            if (!$cachedOtp) {
                throw new OtpNotFoundException();
            }

            if ($otp !== (int)$cachedOtp) {
                throw new InvalidOtpException();
            }
        }
    }

    /**
     * @param $invoice
     * @return void
     */
    public function sendFailedPaymentSms($invoice): void
    {
        SendSms::dispatch(new InvoiceFailedSms($invoice->transaction->user->mobile, $invoice->uuid));
    }
}
