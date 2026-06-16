<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Pencil } from '@lucide/vue';
import { edit } from '@/actions/App/Http/Controllers/AccountController';
import { Button } from '@/components/ui/button';
import { formatCurrency } from '@/composables/useFormatCurrency';
import type { Account } from '@/types';

defineProps<{ account: Account }>();
</script>

<template>
    <Head :title="account.name" />

    <div class="space-y-6 p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    v-if="account.color"
                    class="size-4 rounded-full"
                    :style="{ backgroundColor: account.color }"
                />
                <h1 class="text-2xl font-bold">{{ account.name }}</h1>
            </div>
            <Button variant="outline" size="sm" as-child>
                <Link :href="edit({ account: account.id })">
                    <Pencil class="mr-2 size-4" />
                    Modifier
                </Link>
            </Button>
        </div>

        <div class="rounded-lg border bg-card p-6">
            <p class="text-sm text-muted-foreground">Solde actuel</p>
            <p class="text-4xl font-bold tabular-nums">
                {{ formatCurrency(account.balance_cents, account.currency) }}
            </p>
        </div>

        <div>
            <h2 class="mb-4 text-lg font-semibold">Transactions</h2>
            <p class="text-muted-foreground">
                Les transactions seront disponibles prochainement.
            </p>
        </div>
    </div>
</template>
