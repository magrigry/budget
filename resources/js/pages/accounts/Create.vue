<script setup lang="ts">
import type { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { store } from '@/actions/App/Http/Controllers/AccountController';
import AccountForm from '@/components/AccountForm.vue';

function handleSubmit(form: ReturnType<typeof useForm>) {
    form.post(store.url());
}
</script>

<template>
    <Head title="Nouveau compte" />

    <div class="mx-auto max-w-lg space-y-6 p-4 md:p-6">
        <h1 class="text-2xl font-bold">Nouveau compte</h1>
        <AccountForm submit-label="Créer le compte" :on-submit="handleSubmit" />
    </div>
</template>
