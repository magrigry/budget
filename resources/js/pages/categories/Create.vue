<script setup lang="ts">
import type { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { store } from '@/actions/App/Http/Controllers/CategoryController';
import CategoryForm from '@/components/CategoryForm.vue';

const { t } = useI18n();

function handleSubmit(form: ReturnType<typeof useForm>) {
    form.post(store.url());
}
</script>

<template>
    <Head :title="t('categories.create.title')" />

    <div class="mx-auto max-w-lg space-y-6 p-4 md:p-6">
        <h1 class="text-2xl font-bold">{{ t('categories.create.title') }}</h1>
        <CategoryForm
            :submit-label="t('categories.create.submit')"
            :on-submit="handleSubmit"
        />
    </div>
</template>
