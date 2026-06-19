<script setup lang="ts">
import type { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { store } from '@/actions/App/Http/Controllers/Transaction/TransferController';
import TransferForm from '@/components/TransferForm.vue';

const { t } = useI18n();

const props = defineProps<{
    accounts: App.Data.Account[];
}>();

function handleSubmit(form: ReturnType<typeof useForm>) {
    form.post(store.url());
}
</script>

<template>
    <Head :title="t('transactions.create.title')" />

    <div class="mx-auto max-w-lg space-y-6 p-4 md:p-6">
        <h1 class="text-2xl font-bold">
            {{ t('transactions.form.transfer') }}
        </h1>
        <TransferForm
            :accounts="props.accounts"
            :submit-label="t('transactions.create.submit')"
            :on-submit="handleSubmit"
        />
    </div>
</template>
