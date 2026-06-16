<script setup lang="ts">
import type { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { update } from '@/actions/App/Http/Controllers/AccountController';
import AccountForm from '@/components/AccountForm.vue';

import type { Account } from '@/types';

const props = defineProps<{ account: Account }>();

function handleSubmit(form: ReturnType<typeof useForm>) {
    form.patch(update.url({ account: props.account.id }));
}
</script>

<template>
    <Head title="Modifier le compte" />

    <div class="mx-auto max-w-lg space-y-6 p-4 md:p-6">
        <h1 class="text-2xl font-bold">Modifier le compte</h1>
        <AccountForm
            :account="{
                ...account,
                initial_balance: account.initial_balance_cents / 100,
                color: account.color ?? '',
                icon: account.icon ?? '',
            }"
            submit-label="Enregistrer les modifications"
            lock-initial-balance
            :on-submit="handleSubmit"
        />
    </div>
</template>
