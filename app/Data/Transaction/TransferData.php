<?php

namespace App\Data\Transaction;

use App\Data\AccountData;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('Transfer')]
class TransferData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $from_account_id,
        public readonly int $to_account_id,
        public readonly int $amount_cents,
        public readonly string $label,
        public readonly string $transacted_at,
        public readonly AccountData $from_account,
        public readonly AccountData $to_account,
    ) {}
}
