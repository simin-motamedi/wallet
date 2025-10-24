<?php

namespace Tests\Feature;

use App\Enums\InvoiceState;
use App\Enums\TransactionState;
use App\Enums\UserStatus;
use App\Enums\WalletStatus;
use App\Jobs\SendSms;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Invoice;
use App\Services\Sms\InvoicePaidSms;
use App\Services\Sms\OtpSms;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoicePurchaseFeatureTest extends TestCase
{
    use RefreshDatabase;



    #[Test]
    public function userCanVerifyOtpAndCompletePurchase(): void
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
        $invoice = $invoice->fresh();
        $invoice->setRelation('wallet', $wallet);

        Cache::put('kavenegar' . $user->mobile, 12345, now()->addMinutes(5));

        $response = $this->actingAs($user)->postJson("/api/v1/invoices/{$invoice->uuid}/pay", [
            'otp' => 12345,
        ]);

        $invoice->refresh();
        $invoice->load(['transaction.user', 'wallet']);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'success',
                'data' => [
                    'uuid' => $invoice->uuid,
                    'expiration_time' => $invoice->expiration_time->toISOString(),
                    'state' => 'approved',
                    'paid_at' => $invoice->paid_at->toISOString(),
                    'transaction' => [
                        'uuid' => $invoice->transaction->uuid,
                        'amount' => (string) $invoice->transaction->amount,
                        'state' => $invoice->transaction->state,
                        'created_at' => $invoice->transaction->created_at->toISOString(),
                        'updated_at' => $invoice->transaction->updated_at->toISOString(),
                        'user' => [
                            'first_name' => $invoice->transaction->user->first_name,
                            'last_name' => $invoice->transaction->user->last_name,
                            'mobile' => $invoice->transaction->user->mobile,
                            'email' => $invoice->transaction->user->email,
                            'birth_date' => $invoice->transaction->user->birth_date,
                        ],
                    ],
                    'wallet' => [
                        'balance' => 100000,
                        'status' => $invoice->wallet->status,
                        'created_at' => $invoice->wallet->created_at->toISOString(),
                        'updated_at' => $invoice->wallet->updated_at->toISOString(),
                        'laravel_through_key' => $invoice->wallet->laravel_through_key,
                    ],
                ],
            ]);



        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'state' => InvoiceState::APPROVED->value,
        ]);

        Queue::assertPushed(SendSms::class, fn($job) => $job->message instanceof InvoicePaidSms);
    }
}
