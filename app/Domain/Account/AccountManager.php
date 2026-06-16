<?php

namespace App\Domain\Account;

use App\Models\Account;
use App\Models\User;

class AccountManager
{
    public function store(
        User $user,
        string $name,
        string $currency,
        int $initialBalanceCents,
        ?string $color,
        ?string $icon,
    ): Account {
        /** @var Account */
        return $user->accounts()->create([
            'name' => $name,
            'currency' => $currency,
            'initial_balance_cents' => $initialBalanceCents,
            'color' => $color,
            'icon' => $icon,
        ]);
    }

    public function update(
        Account $account,
        string $name,
        string $currency,
        ?string $color,
        ?string $icon,
    ): void {
        $account->update([
            'name' => $name,
            'currency' => $currency,
            'color' => $color,
            'icon' => $icon,
        ]);
    }
}
