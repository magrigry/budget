<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Pencil } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import { edit } from '@/actions/App/Http/Controllers/AccountController';
import { Button } from '@/components/ui/button';
import { formatCurrency } from '@/composables/useFormatCurrency';


const { t } = useI18n();

defineProps<{ account: App.Data.Account }>();
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
                    {{ t('accounts.show.edit') }}
                </Link>
            </Button>
        </div>

        <div class="rounded-lg border bg-card p-6">
            <p class="text-sm text-muted-foreground">
                {{ t('accounts.show.balance') }}
            </p>
            <p class="text-4xl font-bold tabular-nums">
                {{ formatCurrency(account.balance_cents, account.currency) }}
            </p>
        </div>

        <div>
            <h2 class="mb-4 text-lg font-semibold">
                {{ t('accounts.show.transactions') }}
            </h2>
            <p class="text-muted-foreground">
                {{ t('accounts.show.transactionsSoon') }}
            </p>
        </div>
    </div>
</template>
