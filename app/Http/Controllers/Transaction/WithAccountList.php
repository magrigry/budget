<?php

namespace App\Http\Controllers\Transaction;

use App\Data\AccountData;
use App\Data\CategoryData;
use App\Models\Account;
use App\Models\Category;

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

    /** @return CategoryData[] */
    private function categoryList(): array
    {
        return auth()->user()
            ->categories()
            ->orderBy('name')
            ->get()
            ->map(fn (Category $c) => CategoryData::fromModel($c))
            ->all();
    }
}
