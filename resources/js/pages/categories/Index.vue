<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import {
    create as createEntry,
    destroy as destroyEntry,
    edit as editEntry,
} from '@/actions/App/Http/Controllers/CategoryController';
import CategoryIcon from '@/components/CategoryIcon.vue';
import { Button } from '@/components/ui/button';

const { t } = useI18n();

defineProps<{
    categories: App.Data.Category[];
}>();

function deleteItem(item: App.Data.Category) {
    if (!confirm(t('categories.index.confirmDelete', { name: item.name }))) {
        return;
    }

    router.delete(destroyEntry({ category: item.id }));
}
</script>

<template>
    <Head :title="t('categories.index.title')" />

    <div class="space-y-4 p-4 md:p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">
                {{ t('categories.index.title') }}
            </h1>
            <Button as-child size="sm">
                <Link :href="createEntry()">
                    <Plus class="mr-2 size-4" />
                    {{ t('categories.index.new') }}
                </Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-md border">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th class="w-10 px-4 py-2 text-left font-medium">
                            {{ t('categories.index.columns.color') }}
                        </th>
                        <th class="w-10 px-4 py-2 text-left font-medium">
                            {{ t('categories.index.columns.icon') }}
                        </th>
                        <th class="px-4 py-2 text-left font-medium">
                            {{ t('categories.index.columns.name') }}
                        </th>
                        <th class="px-4 py-2" />
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in categories"
                        :key="item.id"
                        class="border-b last:border-0 hover:bg-muted/30"
                    >
                        <td class="px-4 py-2">
                            <span
                                v-if="item.color"
                                class="inline-block size-5 rounded-full border"
                                :style="{ backgroundColor: item.color }"
                            />
                            <span
                                v-else
                                class="inline-block size-5 rounded-full border bg-muted"
                            />
                        </td>
                        <td class="px-4 py-2">
                            <CategoryIcon
                                :icon="item.icon"
                                class="size-4 text-muted-foreground"
                            />
                        </td>
                        <td class="px-4 py-2 font-medium">{{ item.name }}</td>
                        <td class="px-4 py-2">
                            <div class="flex justify-end gap-1">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="size-7"
                                    as-child
                                >
                                    <Link
                                        :href="editEntry({ category: item.id })"
                                    >
                                        <Pencil class="size-3.5" />
                                    </Link>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="size-7 text-muted-foreground hover:text-destructive"
                                    @click="deleteItem(item)"
                                >
                                    <Trash2 class="size-3.5" />
                                </Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="categories.length === 0">
                        <td
                            colspan="4"
                            class="px-4 py-10 text-center text-muted-foreground"
                        >
                            {{ t('categories.index.empty') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
