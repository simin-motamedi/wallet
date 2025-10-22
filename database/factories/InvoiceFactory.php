<?php

namespace Database\Factories;

use App\Enums\InvoiceState;
use App\Models\invoice;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<invoice>
 */
class InvoiceFactory extends Factory
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
            'expiration_time' => fake()->dateTimeBetween('now', '+5 hours'),
            'state' => InvoiceState::PENDING,
            'paid_at' => null,
        ];
    }
}
