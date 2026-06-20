<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowDown,
    ArrowRight,
    ArrowUp,
    ArrowUpDown,
    Pencil,
    Plus,
    Trash2,
} from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import { index as transactionsIndex } from '@/actions/App/Http/Controllers/Transaction/TransactionController';
import {
    create as createTransfer,
    destroy as destroyTransfer,
    edit as editTransfer,
    index,
} from '@/actions/App/Http/Controllers/Transaction/TransferController';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { formatDate } from '@/composables/useFormatDate';

const { t } = useI18n();

interface Filters {
    account_id: number | null;
    date_from: string | null;
    date_to: string | null;
    search: string | null;
    sort_by: string;
    sort_dir: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedTransfers {
    items: App.Data.Transaction.Transfer[];
    meta: {
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        links: PaginationLink[];
    };
}

const props = defineProps<{
    transfers: PaginatedTransfers;
    accounts: App.Data.Account[];
    filters: Filters;
}>();

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilter(patch: Partial<Filters>) {
    const merged = { ...props.filters, ...patch };
    const params: Record<string, string | number> = {};

    for (const [k, v] of Object.entries(merged)) {
        if (v !== null && v !== undefined && v !== '') {
            params[k] = v as string | number;
        }
    }

    params['_f'] = 1;

    router.get(index(), params, { preserveScroll: true, replace: true });
}

function onSearchInput(e: Event) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilter({ search: (e.target as HTMLInputElement).value || null });
    }, 400);
}

function resetFilters() {
    router.get(index(), { reset: 1 }, { preserveScroll: true });
}

function toggleSort(column: 'transacted_at' | 'amount_cents') {
    const isSame = props.filters.sort_by === column;
    applyFilter({
        sort_by: column,
        sort_dir: isSame && props.filters.sort_dir === 'asc' ? 'desc' : 'asc',
    });
}

function deleteItem(item: App.Data.Transaction.Transfer) {
    if (!confirm('Delete this transfer?')) {
        return;
    }

    router.delete(destroyTransfer({ transfer: item.id }));
}
</script>

<template>
    <Head :title="t('transactions.transfers.index.title')" />

    <div class="space-y-4 p-4 md:p-6">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h1 class="text-2xl font-bold">
                {{ t('transactions.transfers.index.title') }}
            </h1>
            <div class="flex gap-2">
                <Button as-child size="sm" variant="outline">
                    <Link :href="transactionsIndex()">
                        {{ t('transactions.transfers.index.seeEntries') }}
                    </Link>
                </Button>
                <Button as-child size="sm">
                    <Link :href="createTransfer()">
                        <Plus class="mr-2 size-4" />
                        {{ t('transactions.transfers.index.new') }}
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-2">
            <select
                class="h-8 rounded-md border border-input bg-background px-2 text-sm"
                :value="filters.account_id ?? ''"
                @change="
                    applyFilter({
                        account_id: ($event.target as HTMLSelectElement).value
                            ? Number(($event.target as HTMLSelectElement).value)
                            : null,
                    })
                "
            >
                <option value="">
                    {{ t('transactions.index.filters.allAccounts') }}
                </option>
                <option v-for="a in accounts" :key="a.id" :value="a.id">
                    {{ a.name }}
                </option>
            </select>

            <Input
                type="date"
                class="h-8 w-36 text-sm"
                :value="filters.date_from ?? ''"
                @change="
                    applyFilter({
                        date_from:
                            ($event.target as HTMLInputElement).value || null,
                    })
                "
            />

            <Input
                type="date"
                class="h-8 w-36 text-sm"
                :value="filters.date_to ?? ''"
                @change="
                    applyFilter({
                        date_to:
                            ($event.target as HTMLInputElement).value || null,
                    })
                "
            />

            <Input
                type="search"
                class="h-8 w-48 text-sm"
                :placeholder="t('transactions.index.filters.search')"
                :default-value="filters.search ?? ''"
                @input="onSearchInput"
            />

            <Button variant="ghost" size="sm" class="h-8" @click="resetFilters">
                {{ t('transactions.index.filters.reset') }}
            </Button>
        </div>

        <!-- Mobile cards -->
        <div class="divide-y rounded-md border text-sm md:hidden">
            <div
                v-for="item in transfers.items"
                :key="item.id"
                class="px-3 py-2 hover:bg-muted/30"
            >
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <div class="truncate font-medium">{{ item.label }}</div>
                        <div
                            class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-muted-foreground"
                        >
                            <span class="tabular-nums">{{
                                formatDate(item.transacted_at)
                            }}</span>
                            <span
                                :class="
                                    filters.account_id === item.from_account.id
                                        ? 'font-semibold text-foreground'
                                        : ''
                                "
                                >{{ item.from_account.name }}</span
                            >
                            <ArrowRight class="size-3 shrink-0" />
                            <span
                                :class="
                                    filters.account_id === item.to_account.id
                                        ? 'font-semibold text-foreground'
                                        : ''
                                "
                                >{{ item.to_account.name }}</span
                            >
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-1">
                        <span class="text-muted-foreground tabular-nums">{{
                            item.amount_cents / 100
                        }}</span>
                        <div class="flex flex-col gap-0.5">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="size-7"
                                as-child
                            >
                                <Link
                                    :href="editTransfer({ transfer: item.id })"
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
                    </div>
                </div>
            </div>
            <div
                v-if="transfers.items.length === 0"
                class="px-4 py-10 text-center text-muted-foreground"
            >
                {{ t('transactions.transfers.index.empty') }}
            </div>
        </div>

        <!-- Desktop table -->
        <div class="hidden rounded-md border md:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th
                            class="cursor-pointer px-4 py-2 text-left font-medium"
                            @click="toggleSort('transacted_at')"
                        >
                            <span class="flex items-center gap-1">
                                {{ t('transactions.index.columns.date') }}
                                <ArrowDown
                                    v-if="
                                        filters.sort_by === 'transacted_at' &&
                                        filters.sort_dir === 'desc'
                                    "
                                    class="size-3"
                                />
                                <ArrowUp
                                    v-else-if="
                                        filters.sort_by === 'transacted_at'
                                    "
                                    class="size-3"
                                />
                                <ArrowUpDown v-else class="size-3 opacity-40" />
                            </span>
                        </th>
                        <th class="px-4 py-2 text-left font-medium">
                            {{ t('transactions.index.columns.label') }}
                        </th>
                        <th class="px-4 py-2 text-left font-medium">
                            {{ t('transactions.transfers.index.columns.from') }}
                        </th>
                        <th class="px-4 py-2 text-left font-medium">
                            {{ t('transactions.transfers.index.columns.to') }}
                        </th>
                        <th
                            class="cursor-pointer px-4 py-2 text-right font-medium"
                            @click="toggleSort('amount_cents')"
                        >
                            <span class="flex items-center justify-end gap-1">
                                {{ t('transactions.index.columns.amount') }}
                                <ArrowDown
                                    v-if="
                                        filters.sort_by === 'amount_cents' &&
                                        filters.sort_dir === 'desc'
                                    "
                                    class="size-3"
                                />
                                <ArrowUp
                                    v-else-if="
                                        filters.sort_by === 'amount_cents'
                                    "
                                    class="size-3"
                                />
                                <ArrowUpDown v-else class="size-3 opacity-40" />
                            </span>
                        </th>
                        <th class="px-4 py-2" />
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in transfers.items"
                        :key="item.id"
                        class="border-b last:border-0 hover:bg-muted/30"
                    >
                        <td
                            class="px-4 py-2 text-muted-foreground tabular-nums"
                        >
                            {{ formatDate(item.transacted_at) }}
                        </td>
                        <td class="px-4 py-2 font-medium">{{ item.label }}</td>
                        <td
                            class="px-4 py-2"
                            :class="
                                filters.account_id === item.from_account.id
                                    ? 'font-semibold text-foreground'
                                    : 'text-muted-foreground'
                            "
                        >
                            {{ item.from_account.name }}
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="flex items-center gap-1"
                                :class="
                                    filters.account_id === item.to_account.id
                                        ? 'font-semibold text-foreground'
                                        : 'text-muted-foreground'
                                "
                            >
                                <ArrowRight class="size-3" />
                                {{ item.to_account.name }}
                            </span>
                        </td>
                        <td
                            class="px-4 py-2 text-right text-muted-foreground tabular-nums"
                        >
                            {{ item.amount_cents / 100 }}
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex justify-end gap-1">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="size-7"
                                    as-child
                                >
                                    <Link
                                        :href="
                                            editTransfer({ transfer: item.id })
                                        "
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
                    <tr v-if="transfers.items.length === 0">
                        <td
                            colspan="6"
                            class="px-4 py-10 text-center text-muted-foreground"
                        >
                            {{ t('transactions.transfers.index.empty') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="transfers.meta.last_page > 1"
            class="flex items-center justify-center gap-1"
        >
            <Button
                v-for="link in transfers.meta.links"
                :key="link.label"
                :variant="link.active ? 'default' : 'outline'"
                size="sm"
                :disabled="!link.url"
                @click="
                    link.url &&
                    router.get(link.url, {}, { preserveScroll: true })
                "
            >
                <span v-html="link.label" />
            </Button>
        </div>
    </div>
</template>
