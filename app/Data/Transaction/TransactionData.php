<?php

namespace App\Data\Transaction;

use App\Data\AccountData;
use App\Data\CategoryData;
use App\Enums\TransactionType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('Transaction')]
class TransactionData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $account_id,
        public readonly ?int $category_id,
        public readonly TransactionType $type,
        public readonly int $amount_cents,
        public readonly string $label,
        public readonly string $transacted_at,
        public readonly AccountData $account,
        public readonly ?CategoryData $category,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
    ) {}
}
