<?php

namespace App\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum TransactionType: string
{
    case Income = 'income';
    case Expense = 'expense';
}
