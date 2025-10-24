<?php

namespace Tests\Unit;

use App\Enums\InvoiceState;
use App\Enums\TransactionState;
use App\Enums\UserStatus;
use App\Enums\WalletStatus;
use App\Exceptions\InvoiceAlreadyPaidException;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Services\InvoiceTransactionService;
use App\Services\Sms\InvoicePaidSms;
use App\Services\Sms\OtpSms;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendSms;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoiceTransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private InvoiceTransactionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new InvoiceTransactionService();

        Queue::fake();
    }

    #[Test]
    public function validateSendsOtpForValidInvoice(): void
    {
        $user = User::factory(['status'=> UserStatus::ACTIVE->value])->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100000, 'status' => WalletStatus::ACTIVE->value]);
        $invoice = Invoice::factory()->create(['state'=> InvoiceState::PENDING->value]);
        Transaction::factory()->create([
            'transactionable_type' => Invoice::class,
            'transactionable_id' => $invoice->id,
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => 100,
            'state' => TransactionState::PENDING->value
        ]);

        $this->service->validate($invoice);

        Queue::assertPushed(SendSms::class, fn($job) => $job->message instanceof OtpSms);
    }

    #[Test]
    public function validateThrowsIfInvoiceAlreadyPaid(): void
    {
        $this->expectException(InvoiceAlreadyPaidException::class);

        $user = User::factory(['status'=> UserStatus::ACTIVE->value])->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100000, 'status' => WalletStatus::ACTIVE->value]);
        $invoice = Invoice::factory()->create(['state' => InvoiceState::APPROVED->value]);
        Transaction::factory()->create([
            'transactionable_type' => Invoice::class,
            'transactionable_id' => $invoice->id,
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => 100,
            'state' => TransactionState::PENDING->value
        ]);

        $this->service->validate($invoice);
    }


    #[Test]
    public function purchaseUpdatesWalletInvoiceAndTransaction(): void
    {
        $user = User::factory(['status'=> UserStatus::ACTIVE->value])->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 100000,
            'status' => WalletStatus::ACTIVE->value
        ]);
        $invoice = Invoice::factory()->create(['state' => InvoiceState::PENDING->value]);
        $transaction = Transaction::factory()->create([
            'transactionable_type' => Invoice::class,
            'transactionable_id' => $invoice->id,
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => 100,
            'state' => TransactionState::PENDING->value
        ]);

        Queue::fake();

        $serviceMock = $this->getMockBuilder(InvoiceTransactionService::class)
            ->onlyMethods(['mockPaymentApi', 'validate'])
            ->getMock();

        $serviceMock->expects($this->once())->method('validate');

        $serviceMock->method('mockPaymentApi')->willReturn([
            'success' => true,
            'message' => 'payment successful'
        ]);

        $cacheKey = 'kavenegar' . $user->mobile;
        Cache::put($cacheKey, 12345, now()->addMinutes(5));

        $serviceMock->process($invoice, 12345);

        $this->assertEquals(InvoiceState::APPROVED->value, $invoice->fresh()->state);
        $this->assertEquals(TransactionState::APPROVED->value, $transaction->fresh()->state);
        $this->assertEquals(WalletStatus::ACTIVE->value, $wallet->fresh()->status);

        Queue::assertPushed(SendSms::class, fn($job) => $job->message instanceof InvoicePaidSms);
    }

    #[Test]
    public function processThrowsIfPaymentFails(): void
    {
        $this->expectException(\Exception::class);

        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 1000]);
        $invoice = Invoice::factory()->create();
        Transaction::factory()->create([
            'transactionable_type' => Invoice::class,
            'transactionable_id' => $invoice->id,
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => 100,
            'state' => TransactionState::PENDING->value
        ]);

        $serviceMock = $this->getMockBuilder(InvoiceTransactionService::class)
            ->onlyMethods(['mockPaymentApi'])
            ->getMock();

        $serviceMock->method('mockPaymentApi')->willReturn(['success' => false, 'message' => 'payment failed']);

        $serviceMock->process($invoice, 1234);
    }
}
