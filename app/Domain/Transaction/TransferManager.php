<?php

namespace App\Domain\Transaction;

use App\Models\Transfer;
use App\Models\User;
use Carbon\CarbonImmutable;

class TransferManager
{
    public function store(
        User $user,
        int $fromAccountId,
        int $toAccountId,
        int $amountCents,
        string $label,
        CarbonImmutable $transactedAt,
    ): Transfer {
        return Transfer::create([
            'from_account_id' => $fromAccountId,
            'to_account_id' => $toAccountId,
            'amount_cents' => $amountCents,
            'label' => $label,
            'transacted_at' => $transactedAt,
        ]);
    }

    public function update(
        Transfer $transfer,
        int $fromAccountId,
        int $toAccountId,
        int $amountCents,
        string $label,
        CarbonImmutable $transactedAt,
    ): void {
        $transfer->update([
            'from_account_id' => $fromAccountId,
            'to_account_id' => $toAccountId,
            'amount_cents' => $amountCents,
            'label' => $label,
            'transacted_at' => $transactedAt,
        ]);
    }

    public function delete(Transfer $transfer): void
    {
        $transfer->delete();
    }
}
