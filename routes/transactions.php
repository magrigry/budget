<?php

use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Transaction\TransferController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('transactions/transfers', TransferController::class)
        ->except(['show'])
        ->names('transactions.transfers');

    Route::resource('transactions', TransactionController::class)
        ->except(['show'])
        ->names('transactions');
});
