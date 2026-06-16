<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->randomElement(['Compte courant', 'Livret A', 'Compte épargne', 'Carte Visa']),
            'currency' => 'EUR',
            'initial_balance_cents' => $this->faker->numberBetween(-50000, 1000000),
            'color' => $this->faker->hexColor(),
            'icon' => null,
        ];
    }
}
