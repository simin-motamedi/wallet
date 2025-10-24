<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wallet::all()->each(function ($wallet) {
            $transaction = Transaction::factory()->create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'amount' => fake()->numberBetween(100, $wallet->balance),
                'transactionable_type' => Invoice::class,
            ]);

            Invoice::factory()->create([
                'id' => $transaction->transactionable_id ?? null,
            ]);
        });
    }
}
