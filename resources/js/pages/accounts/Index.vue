<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Archive, ArchiveRestore, Pencil, Plus } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import {
    create,
    destroy,
    edit,
    index,
    restore,
    show,
} from '@/actions/App/Http/Controllers/AccountController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { formatCurrency } from '@/composables/useFormatCurrency';

const { t } = useI18n();

const props = defineProps<{
    accounts: App.Data.Account[];
    showArchived: boolean;
}>();

function toggleArchived() {
    router.get(
        index(),
        { archived: props.showArchived ? undefined : true },
        { preserveScroll: true },
    );
}

function archiveAccount(id: number) {
    router.delete(destroy({ account: id }));
}

function restoreAccount(id: number) {
    router.patch(restore({ id }));
}
</script>

<template>
    <Head :title="t('accounts.index.title')" />

    <div class="space-y-6 p-4 md:p-6">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h1 class="text-2xl font-bold">{{ t('accounts.index.title') }}</h1>
            <div class="flex gap-2">
                <Button variant="outline" size="sm" @click="toggleArchived">
                    <Archive class="mr-2 size-4" />
                    {{
                        showArchived
                            ? t('accounts.index.hideArchived')
                            : t('accounts.index.showArchived')
                    }}
                </Button>
                <Button as-child size="sm">
                    <Link :href="create()">
                        <Plus class="mr-2 size-4" />
                        {{ t('accounts.index.new') }}
                    </Link>
                </Button>
            </div>
        </div>

        <div
            v-if="accounts.length === 0"
            class="py-12 text-center text-muted-foreground"
        >
            {{
                t(
                    showArchived
                        ? 'accounts.index.emptyArchived'
                        : 'accounts.index.empty',
                )
            }}
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Card
                v-for="account in accounts"
                :key="account.id"
                class="relative overflow-hidden"
                :class="account.archived ? 'opacity-60' : ''"
            >
                <div
                    v-if="account.color"
                    class="absolute top-0 left-0 h-full w-1"
                    :style="{ backgroundColor: account.color }"
                />
                <CardHeader class="pb-2 pl-5">
                    <div class="flex items-start justify-between">
                        <CardTitle class="text-base">{{
                            account.name
                        }}</CardTitle>
                        <div class="flex gap-1">
                            <Button
                                v-if="account.archived"
                                variant="ghost"
                                size="icon"
                                class="size-8"
                                @click="restoreAccount(account.id)"
                            >
                                <ArchiveRestore class="size-4" />
                            </Button>
                            <template v-else>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="size-8"
                                    as-child
                                >
                                    <Link :href="edit({ account: account.id })">
                                        <Pencil class="size-4" />
                                    </Link>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="size-8 text-muted-foreground"
                                    @click="archiveAccount(account.id)"
                                >
                                    <Archive class="size-4" />
                                </Button>
                            </template>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="pl-5">
                    <Link :href="show({ account: account.id })" class="block">
                        <p class="text-2xl font-semibold tabular-nums">
                            {{
                                formatCurrency(
                                    account.balance_cents,
                                    account.currency,
                                )
                            }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ account.currency }}
                        </p>
                    </Link>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
