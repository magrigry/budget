<?php

namespace App\Data;

use App\Models\Account;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('Account')]
class AccountData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $currency,
        public readonly int $initial_balance_cents,
        public readonly int $balance_cents,
        public readonly ?string $color,
        public readonly ?string $icon,
        public readonly bool $archived,
    ) {}

    public static function fromModel(Account $account): self
    {
        return new self(
            id: $account->id,
            name: $account->name,
            currency: $account->currency,
            initial_balance_cents: $account->initial_balance_cents,
            balance_cents: $account->balance_cents,
            color: $account->color,
            icon: $account->icon,
            archived: $account->trashed(),
        );
    }
}
