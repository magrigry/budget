<script setup lang="ts">
import type { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { update } from '@/actions/App/Http/Controllers/AccountController';
import AccountForm from '@/components/AccountForm.vue';



const { t } = useI18n();

const props = defineProps<{ account: App.Data.Account }>();

function handleSubmit(form: ReturnType<typeof useForm>) {
    form.patch(update.url({ account: props.account.id }));
}
</script>

<template>
    <Head :title="t('accounts.edit.title')" />

    <div class="mx-auto max-w-lg space-y-6 p-4 md:p-6">
        <h1 class="text-2xl font-bold">{{ t('accounts.edit.title') }}</h1>
        <AccountForm
            :account="{
                ...account,
                initial_balance: account.initial_balance_cents / 100,
                color: account.color ?? '',
                icon: account.icon ?? '',
            }"
            :submit-label="t('accounts.edit.submit')"
            lock-initial-balance
            :on-submit="handleSubmit"
        />
    </div>
</template>
