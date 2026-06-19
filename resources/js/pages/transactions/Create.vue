<script setup lang="ts">
import type { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { store } from '@/actions/App/Http/Controllers/Transaction/TransactionController';
import TransactionForm from '@/components/TransactionForm.vue';

const { t } = useI18n();

const props = defineProps<{
    accounts: App.Data.Account[];
    categories: App.Data.Category[];
}>();

function handleSubmit(form: ReturnType<typeof useForm>) {
    form.post(store.url());
}
</script>

<template>
    <Head :title="t('transactions.create.title')" />

    <div class="mx-auto max-w-lg space-y-6 p-4 md:p-6">
        <h1 class="text-2xl font-bold">{{ t('transactions.create.title') }}</h1>
        <TransactionForm
            :accounts="props.accounts"
            :categories="props.categories"
            :submit-label="t('transactions.create.submit')"
            :on-submit="handleSubmit"
        />
    </div>
</template>
