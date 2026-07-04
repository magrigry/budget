<script setup lang="ts">
import { computed } from 'vue';
import { CATEGORY_ICONS } from '@/lib/categoryIcons';

const props = defineProps<{
    icon: string | null | undefined;
    class?: string;
}>();

const iconComponent = computed(() =>
    props.icon ? (CATEGORY_ICONS[props.icon] ?? null) : null,
);
</script>

<template>
    <component
        :is="iconComponent"
        v-if="iconComponent"
        :class="props.class ?? 'size-4'"
    />
</template>
