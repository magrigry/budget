<script setup lang="ts">
import type { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { update } from '@/actions/App/Http/Controllers/Transaction/TransactionController';
import TransactionForm from '@/components/TransactionForm.vue';

const { t } = useI18n();

const props = defineProps<{
    transaction: App.Data.Transaction.Transaction;
    accounts: App.Data.Account[];
}>();

function handleSubmit(form: ReturnType<typeof useForm>) {
    form.patch(update.url({ transaction: props.transaction.id }));
}
</script>

<template>
    <Head :title="t('transactions.edit.title')" />

    <div class="mx-auto max-w-lg space-y-6 p-4 md:p-6">
        <h1 class="text-2xl font-bold">{{ t('transactions.edit.title') }}</h1>
        <TransactionForm
            :accounts="props.accounts"
            :transaction="transaction"
            :submit-label="t('transactions.edit.submit')"
            :on-submit="handleSubmit"
        />
    </div>
</template>
