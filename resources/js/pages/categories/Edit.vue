<script setup lang="ts">
import type { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { update } from '@/actions/App/Http/Controllers/CategoryController';
import CategoryForm from '@/components/CategoryForm.vue';

const { t } = useI18n();

const props = defineProps<{
    category: App.Data.Category;
}>();

function handleSubmit(form: ReturnType<typeof useForm>) {
    form.patch(update.url({ category: props.category.id }));
}
</script>

<template>
    <Head :title="t('categories.edit.title')" />

    <div class="mx-auto max-w-lg space-y-6 p-4 md:p-6">
        <h1 class="text-2xl font-bold">{{ t('categories.edit.title') }}</h1>
        <CategoryForm
            :category="category"
            :submit-label="t('categories.edit.submit')"
            :on-submit="handleSubmit"
        />
    </div>
</template>
