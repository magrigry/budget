<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transfer>
 */
class TransferFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_account_id' => Account::factory(),
            'to_account_id' => Account::factory(),
            'amount_cents' => $this->faker->numberBetween(100, 100000),
            'label' => $this->faker->sentence(3),
            'transacted_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ];
    }
}
