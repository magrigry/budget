<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import CategoryIcon from '@/components/CategoryIcon.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { CATEGORY_ICONS } from '@/lib/categoryIcons';

const { t } = useI18n();

const model = defineModel<string>({ default: '' });

const iconKeys = Object.keys(CATEGORY_ICONS);
</script>

<template>
    <div class="space-y-2">
        <Label>
            {{ t('categories.form.icon') }}
            <span class="text-sm text-muted-foreground">{{
                t('categories.form.iconOptional')
            }}</span>
        </Label>
        <div class="flex flex-wrap gap-1.5">
            <button
                v-for="key in iconKeys"
                :key="key"
                type="button"
                class="flex size-9 items-center justify-center rounded-md border transition-colors"
                :class="
                    model === key
                        ? 'border-primary bg-primary text-primary-foreground'
                        : 'border-input bg-background text-muted-foreground hover:border-primary hover:text-foreground'
                "
                :title="key.replace(/_/g, ' ')"
                @click="model = model === key ? '' : key"
            >
                <CategoryIcon :icon="key" class="size-4" />
            </button>
        </div>
        <Button
            v-if="model"
            type="button"
            variant="ghost"
            size="sm"
            @click="model = ''"
        >
            {{ t('categories.form.iconClear') }}
        </Button>
    </div>
</template>
