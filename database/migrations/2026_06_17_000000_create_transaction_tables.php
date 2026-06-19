<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20); // income | expense
            $table->bigInteger('amount_cents'); // always positive
            $table->string('label');
            $table->date('transacted_at');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('to_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->bigInteger('amount_cents'); // always positive
            $table->string('label');
            $table->date('transacted_at');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('transactions');
    }
};
