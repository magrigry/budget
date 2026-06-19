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
        ?int $categoryId = null,
    ): Transaction {
        return Transaction::create([
            'user_id' => $user->id,
            'account_id' => $accountId,
            'category_id' => $categoryId,
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
        ?int $categoryId = null,
    ): void {
        $transaction->update([
            'account_id' => $accountId,
            'category_id' => $categoryId,
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
