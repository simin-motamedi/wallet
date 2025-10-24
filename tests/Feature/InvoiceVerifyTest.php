<?php

namespace Tests\Feature;

use App\Enums\InvoiceState;
use App\Enums\TransactionState;
use App\Enums\UserStatus;
use App\Enums\WalletStatus;
use App\Jobs\SendSms;
use App\Models\Transaction;
use App\Services\Sms\InvoicePaidSms;
use App\Services\Sms\OtpSms;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Wallet;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceVerifyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function userCanPurchaseInvoiceAndSmsIsSent(): void
    {
        Queue::fake();
        $user = User::factory(['status' => UserStatus::ACTIVE->value])->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 100000,
            'status' => WalletStatus::ACTIVE->value
        ]);
        $invoice = Invoice::factory()->create(['state' => InvoiceState::PENDING->value]);
        Transaction::factory()->create([
            'transactionable_type' => Invoice::class,
            'transactionable_id' => $invoice->id,
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => 100,
            'state' => TransactionState::PENDING->value
        ]);
        $response = $this->actingAs($user)->postJson("/api/v1/invoices/{$invoice->uuid}/prepare");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'success',
                'data' => [
                    'message' => 'OTP sent successfully.'
                ],
            ]);

        Queue::assertPushed(SendSms::class, fn($job) => $job->message instanceof OtpSms);
    }
}
