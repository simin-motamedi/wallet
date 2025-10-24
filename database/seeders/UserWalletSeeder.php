<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class UserWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(5)
            ->has(Wallet::factory()->count(1))
            ->create()
            ->each(function (User $user) {
                $wallet = $user->wallets()->first();

                $invoice = Invoice::factory()->create();

                Transaction::factory()
                    ->for($user)
                    ->for($wallet)
                    ->for($invoice, 'transactionable')
                    ->create();
            });
    }
}
