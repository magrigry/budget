<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Couvre la pagination filtrée (user + date + tri)
            $table->index(['user_id', 'transacted_at'], 'transactions_user_date_idx');
            // Couvre la requête d'agrégat par période (user + type + montant)
            $table->index(['user_id', 'type', 'amount_cents', 'transacted_at'], 'transactions_user_aggregate_idx');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_user_date_idx');
            $table->dropIndex('transactions_user_aggregate_idx');
        });
    }
};
