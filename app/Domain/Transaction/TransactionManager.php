<?php

namespace App\Domain\Transaction;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;

class TransactionManager
{
    public function store(
        User $user,
        int $accountId,
        TransactionType $type,
        int $amountCents,
        string $label,
        CarbonImmutable $transactedAt,
    ): Transaction {
        return Transaction::create([
            'user_id' => $user->id,
            'account_id' => $accountId,
            'type' => $type,
            'amount_cents' => $amountCents,
            'label' => $label,
            'transacted_at' => $transactedAt,
        ]);
    }

    public function update(
        Transaction $transaction,
        int $accountId,
        int $amountCents,
        string $label,
        CarbonImmutable $transactedAt,
    ): void {
        $transaction->update([
            'account_id' => $accountId,
            'amount_cents' => $amountCents,
            'label' => $label,
            'transacted_at' => $transactedAt,
        ]);
    }

    public function delete(Transaction $transaction): void
    {
        $transaction->delete();
    }
}
