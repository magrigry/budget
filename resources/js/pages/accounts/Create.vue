<script setup lang="ts">
import type { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { store } from '@/actions/App/Http/Controllers/AccountController';
import AccountForm from '@/components/AccountForm.vue';

const { t } = useI18n();

function handleSubmit(form: ReturnType<typeof useForm>) {
    form.post(store.url());
}
</script>

<template>
    <Head :title="t('accounts.create.title')" />

    <div class="mx-auto max-w-lg space-y-6 p-4 md:p-6">
        <h1 class="text-2xl font-bold">{{ t('accounts.create.title') }}</h1>
        <AccountForm
            :submit-label="t('accounts.create.submit')"
            :on-submit="handleSubmit"
        />
    </div>
</template>
