<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('accounts', AccountController::class);
    Route::patch('accounts/{id}/restore', [AccountController::class, 'restore'])->name('accounts.restore');
});
