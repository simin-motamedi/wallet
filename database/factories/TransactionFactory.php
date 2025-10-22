<?php

namespace Database\Factories;

use App\Enums\TransactionState;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'transactionable_type' => Invoice::class,
            'transactionable_id' => fake()->randomElement(Invoice::all()->pluck('id')->toArray()),
            'user_id' => fake()->randomElement(User::pluck('id')->toArray()),
            'wallet_id' => fake()->randomElement(Wallet::all()->pluck('id')->toArray()),
            'amount' => fake()->randomFloat(2, 10),
            'state' => fake()->randomElement(array_column(TransactionState::cases(), 'value')),
        ];
    }
}
