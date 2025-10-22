<?php

namespace Database\Factories;

use App\Enums\WalletStatus;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->randomElement(User::pluck('id')->toArray()),
            'balance' => fake()->randomFloat(2, 10, 100),
            'status' => fake()->randomElement(array_column(WalletStatus::cases(), 'value')),
        ];
    }
}
