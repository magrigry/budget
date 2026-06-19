<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { index } from '@/actions/App/Http/Controllers/CategoryController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const { t } = useI18n();

interface FormFields {
    name: string;
    color: string;
}

const props = withDefaults(
    defineProps<{
        category?: App.Data.Category;
        submitLabel?: string;
        onSubmit: (form: ReturnType<typeof useForm<FormFields>>) => void;
    }>(),
    { submitLabel: undefined, category: undefined },
);

const form = useForm<FormFields>({
    name: props.category?.name ?? '',
    color: props.category?.color ?? '',
});

form.transform((data) => ({
    name: data.name,
    color: data.color || null,
}));

function handleSubmit() {
    props.onSubmit(form);
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="handleSubmit">
        <div class="space-y-2">
            <Label for="name">{{ t('categories.form.name') }}</Label>
            <Input
                id="name"
                v-model="form.name"
                type="text"
                :placeholder="t('categories.form.namePlaceholder')"
                required
            />
            <InputError :message="form.errors.name" />
        </div>

        <div class="space-y-2">
            <Label>
                {{ t('categories.form.color') }}
                <span class="text-sm text-muted-foreground">{{
                    t('categories.form.colorOptional')
                }}</span>
            </Label>
            <div class="flex items-center gap-3">
                <template v-if="form.color">
                    <input
                        :value="form.color"
                        type="color"
                        class="size-9 cursor-pointer rounded-md border border-input bg-background p-1"
                        @change="
                            form.color = (
                                $event.target as HTMLInputElement
                            ).value
                        "
                    />
                    <Input
                        v-model="form.color"
                        type="text"
                        placeholder="#3b82f6"
                        class="w-32 font-mono text-sm"
                        maxlength="7"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        @click="form.color = ''"
                    >
                        {{ t('categories.form.colorClear') }}
                    </Button>
                </template>
                <Button
                    v-else
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="form.color = '#3b82f6'"
                >
                    {{ t('categories.form.colorPick') }}
                </Button>
            </div>
            <InputError :message="form.errors.color" />
        </div>

        <div class="flex justify-end gap-3">
            <Button as-child variant="outline">
                <Link :href="index()">{{ t('categories.form.cancel') }}</Link>
            </Button>
            <Button type="submit" :disabled="form.processing">
                {{ submitLabel ?? t('categories.form.save') }}
            </Button>
        </div>
    </form>
</template>
