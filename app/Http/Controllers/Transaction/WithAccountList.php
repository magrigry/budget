<?php

namespace App\Http\Controllers\Transaction;

use App\Data\AccountData;
use App\Models\Account;

trait WithAccountList
{
    /** @return AccountData[] */
    private function accountList(): array
    {
        return auth()->user()
            ->accounts()
            ->orderBy('name')
            ->get()
            ->map(fn (Account $a) => AccountData::fromModel($a))
            ->all();
    }
}
