<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowDown,
    ArrowUp,
    ArrowUpDown,
    Pencil,
    Plus,
    Trash2,
} from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import {
    create as createEntry,
    destroy as destroyEntry,
    edit as editEntry,
    index,
} from '@/actions/App/Http/Controllers/Transaction/TransactionController';
import { index as transfersIndex } from '@/actions/App/Http/Controllers/Transaction/TransferController';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useBrowserLocale } from '@/composables/useBrowserLocale';
import { formatDate } from '@/composables/useFormatDate';

const { t } = useI18n();
const locale = useBrowserLocale();

type GroupBy = 'none' | 'day' | 'week' | 'month' | 'year';

interface Filters {
    account_id: number | null;
    category_id: number | null;
    type: string | null;
    date_from: string | null;
    date_to: string | null;
    search: string | null;
    sort_by: string;
    sort_dir: string;
    group_by: GroupBy;
}

interface PeriodTotal {
    income: number;
    expense: number;
    net: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedTransactions {
    items: App.Data.Transaction.Transaction[];
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
    transactions: PaginatedTransactions;
    accounts: App.Data.Account[];
    categories: App.Data.Category[];
    filters: Filters;
    totals: Record<string, PeriodTotal>;
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

function deleteItem(item: App.Data.Transaction.Transaction) {
    if (!confirm('Delete this transaction?')) {
        return;
    }

    router.delete(destroyEntry({ transaction: item.id }));
}

// --- Grouping helpers ---

function periodKey(date: string): string {
    const groupBy = props.filters.group_by;

    if (groupBy === 'none') {
        return '';
    }

    if (groupBy === 'day') {
        return date.substring(0, 10);
    }

    if (groupBy === 'year') {
        return date.substring(0, 4);
    }

    if (groupBy === 'week') {
        // Match SQLite strftime('%Y-%W', ...) — weeks start Monday, Jan 1 is week 00
        const d = new Date(date + 'T12:00:00');
        const jan1 = new Date(d.getFullYear(), 0, 1);
        const jan1Day = (jan1.getDay() + 6) % 7; // Mon=0
        const dayOfYear = Math.floor((d.getTime() - jan1.getTime()) / 86400000);
        const week = Math.floor((dayOfYear + jan1Day) / 7);

        return `${d.getFullYear()}-${String(week).padStart(2, '0')}`;
    }

    // month
    return date.substring(0, 7);
}

function periodLabel(key: string): string {
    const groupBy = props.filters.group_by;

    if (groupBy === 'day') {
        return formatDate(key);
    }

    if (groupBy === 'year') {
        return key;
    }

    if (groupBy === 'week') {
        const [year, week] = key.split('-');

        return `${t('transactions.index.groupBy.weekLabel')} ${parseInt(week) + 1} — ${year}`;
    }

    // month: YYYY-MM
    const [year, month] = key.split('-');

    return new Intl.DateTimeFormat(locale, {
        month: 'long',
        year: 'numeric',
    }).format(new Date(parseInt(year), parseInt(month) - 1, 1));
}

function formatAmount(cents: number, signed = false): string {
    const value = cents / 100;
    const formatted = new Intl.NumberFormat(locale, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Math.abs(value));

    if (signed) {
        return value >= 0 ? `+${formatted}` : `−${formatted}`;
    }

    return formatted;
}

function isNewPeriod(idx: number): boolean {
    if (props.filters.group_by === 'none') {
        return false;
    }

    if (idx === 0) {
        return true;
    }

    return (
        periodKey(props.transactions.items[idx].transacted_at) !==
        periodKey(props.transactions.items[idx - 1].transacted_at)
    );
}
</script>

<template>
    <Head :title="t('transactions.index.title')" />

    <div class="space-y-4 p-4 md:p-6">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h1 class="text-2xl font-bold">
                {{ t('transactions.index.title') }}
            </h1>
            <div class="flex gap-2">
                <Button as-child size="sm" variant="outline">
                    <Link :href="transfersIndex()">
                        {{ t('transactions.index.seeTransfers') }}
                    </Link>
                </Button>
                <Button as-child size="sm">
                    <Link :href="createEntry()">
                        <Plus class="mr-2 size-4" />
                        {{ t('transactions.index.new') }}
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

            <select
                class="h-8 rounded-md border border-input bg-background px-2 text-sm"
                :value="filters.category_id ?? ''"
                @change="
                    applyFilter({
                        category_id: ($event.target as HTMLSelectElement).value
                            ? Number(($event.target as HTMLSelectElement).value)
                            : null,
                    })
                "
            >
                <option value="">
                    {{ t('transactions.index.filters.allCategories') }}
                </option>
                <option v-for="c in categories" :key="c.id" :value="c.id">
                    {{ c.name }}
                </option>
            </select>

            <select
                class="h-8 rounded-md border border-input bg-background px-2 text-sm"
                :value="filters.type ?? ''"
                @change="
                    applyFilter({
                        type:
                            ($event.target as HTMLSelectElement).value || null,
                    })
                "
            >
                <option value="">
                    {{ t('transactions.index.filters.allTypes') }}
                </option>
                <option value="income">
                    {{ t('transactions.index.types.income') }}
                </option>
                <option value="expense">
                    {{ t('transactions.index.types.expense') }}
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

            <select
                class="h-8 rounded-md border border-input bg-background px-2 text-sm"
                :value="filters.group_by"
                @change="
                    applyFilter({
                        group_by: (($event.target as HTMLSelectElement).value ||
                            'none') as GroupBy,
                    })
                "
            >
                <option
                    v-for="opt in [
                        'none',
                        'day',
                        'week',
                        'month',
                        'year',
                    ] as GroupBy[]"
                    :key="opt"
                    :value="opt"
                >
                    {{ t(`transactions.index.groupBy.${opt}`) }}
                </option>
            </select>

            <Button variant="ghost" size="sm" class="h-8" @click="resetFilters">
                {{ t('transactions.index.filters.reset') }}
            </Button>
        </div>

        <!-- Mobile cards -->
        <div class="md:hidden rounded-md border divide-y text-sm">
            <template
                v-for="(item, idx) in transactions.items"
                :key="item.id"
            >
                <!-- Period header -->
                <div
                    v-if="isNewPeriod(idx)"
                    class="bg-muted/70 px-3 py-1.5 flex items-center justify-between gap-2"
                >
                    <span class="text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                        {{ periodLabel(periodKey(item.transacted_at)) }}
                    </span>
                    <span class="flex items-center gap-2 text-xs font-semibold tabular-nums">
                        <span class="text-green-600 dark:text-green-400">
                            +{{ formatAmount(totals[periodKey(item.transacted_at)]?.income ?? 0) }}
                        </span>
                        <span class="text-red-600 dark:text-red-400">
                            −{{ formatAmount(totals[periodKey(item.transacted_at)]?.expense ?? 0) }}
                        </span>
                        <span
                            :class="{
                                'text-green-600 dark:text-green-400': (totals[periodKey(item.transacted_at)]?.net ?? 0) >= 0,
                                'text-red-600 dark:text-red-400': (totals[periodKey(item.transacted_at)]?.net ?? 0) < 0,
                            }"
                        >
                            = {{ formatAmount(totals[periodKey(item.transacted_at)]?.net ?? 0, true) }}
                        </span>
                    </span>
                </div>

                <!-- Transaction card -->
                <div class="px-3 py-2 hover:bg-muted/30">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="font-medium truncate">{{ item.label }}</div>
                            <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-muted-foreground">
                                <span class="tabular-nums">{{ formatDate(item.transacted_at) }}</span>
                                <span>{{ item.account.name }}</span>
                                <span
                                    v-if="item.category"
                                    class="inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 font-medium"
                                    :style="item.category.color ? { backgroundColor: item.category.color + '22', color: item.category.color } : {}"
                                    :class="!item.category.color ? 'bg-muted text-muted-foreground' : ''"
                                >
                                    <span v-if="item.category.color" class="size-1.5 rounded-full" :style="{ backgroundColor: item.category.color }" />
                                    {{ item.category.name }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <div class="text-right">
                                <div
                                    class="font-medium tabular-nums"
                                    :class="{
                                        'text-green-600 dark:text-green-400': item.type === 'income',
                                        'text-red-600 dark:text-red-400': item.type === 'expense',
                                    }"
                                >
                                    {{ item.type === 'income' ? '+' : '−' }}{{ formatAmount(item.amount_cents) }}
                                </div>
                                <div class="mt-0.5 flex justify-end">
                                    <span
                                        class="inline-flex items-center rounded-full px-1.5 py-0.5 text-xs font-medium"
                                        :class="{
                                            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': item.type === 'income',
                                            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': item.type === 'expense',
                                        }"
                                    >
                                        {{ t(`transactions.index.types.${item.type}`) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <Button variant="ghost" size="icon" class="size-7" as-child>
                                    <Link :href="editEntry({ transaction: item.id })">
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
            </template>
            <div v-if="transactions.items.length === 0" class="px-4 py-10 text-center text-muted-foreground">
                {{ t('transactions.index.empty') }}
            </div>
        </div>

        <!-- Desktop table -->
        <div class="hidden md:block rounded-md border">
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
                                    v-if="filters.sort_by === 'transacted_at' && filters.sort_dir === 'desc'"
                                    class="size-3"
                                />
                                <ArrowUp
                                    v-else-if="filters.sort_by === 'transacted_at'"
                                    class="size-3"
                                />
                                <ArrowUpDown v-else class="size-3 opacity-40" />
                            </span>
                        </th>
                        <th class="px-4 py-2 text-left font-medium">
                            {{ t('transactions.index.columns.label') }}
                        </th>
                        <th class="px-4 py-2 text-left font-medium">
                            {{ t('transactions.index.columns.category') }}
                        </th>
                        <th class="px-4 py-2 text-left font-medium">
                            {{ t('transactions.index.columns.account') }}
                        </th>
                        <th class="px-4 py-2 text-left font-medium">
                            {{ t('transactions.index.columns.type') }}
                        </th>
                        <th
                            class="cursor-pointer px-4 py-2 text-right font-medium"
                            @click="toggleSort('amount_cents')"
                        >
                            <span class="flex items-center justify-end gap-1">
                                {{ t('transactions.index.columns.amount') }}
                                <ArrowDown
                                    v-if="filters.sort_by === 'amount_cents' && filters.sort_dir === 'desc'"
                                    class="size-3"
                                />
                                <ArrowUp
                                    v-else-if="filters.sort_by === 'amount_cents'"
                                    class="size-3"
                                />
                                <ArrowUpDown v-else class="size-3 opacity-40" />
                            </span>
                        </th>
                        <th class="px-4 py-2" />
                    </tr>
                </thead>
                <tbody>
                    <template
                        v-for="(item, idx) in transactions.items"
                        :key="item.id"
                    >
                        <!-- Period header row -->
                        <tr v-if="isNewPeriod(idx)" class="border-b bg-muted/70">
                            <td
                                colspan="5"
                                class="px-4 py-1.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                            >
                                {{ periodLabel(periodKey(item.transacted_at)) }}
                            </td>
                            <td class="px-4 py-1.5 text-right tabular-nums">
                                <span class="flex items-center justify-end gap-3 text-xs font-semibold">
                                    <span class="text-green-600 dark:text-green-400">
                                        +{{ formatAmount(totals[periodKey(item.transacted_at)]?.income ?? 0) }}
                                    </span>
                                    <span class="text-red-600 dark:text-red-400">
                                        −{{ formatAmount(totals[periodKey(item.transacted_at)]?.expense ?? 0) }}
                                    </span>
                                    <span
                                        :class="{
                                            'text-green-600 dark:text-green-400': (totals[periodKey(item.transacted_at)]?.net ?? 0) >= 0,
                                            'text-red-600 dark:text-red-400': (totals[periodKey(item.transacted_at)]?.net ?? 0) < 0,
                                        }"
                                    >
                                        = {{ formatAmount(totals[periodKey(item.transacted_at)]?.net ?? 0, true) }}
                                    </span>
                                </span>
                            </td>
                            <td class="px-4 py-1.5" />
                        </tr>

                        <!-- Transaction row -->
                        <tr class="border-b last:border-0 hover:bg-muted/30">
                            <td class="px-4 py-2 text-muted-foreground tabular-nums">
                                {{ formatDate(item.transacted_at) }}
                            </td>
                            <td class="px-4 py-2 font-medium">
                                {{ item.label }}
                            </td>
                            <td class="px-4 py-2">
                                <span
                                    v-if="item.category"
                                    class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-medium"
                                    :style="item.category.color ? { backgroundColor: item.category.color + '22', color: item.category.color } : {}"
                                    :class="!item.category.color ? 'bg-muted text-muted-foreground' : ''"
                                >
                                    <span
                                        v-if="item.category.color"
                                        class="size-1.5 rounded-full"
                                        :style="{ backgroundColor: item.category.color }"
                                    />
                                    {{ item.category.name }}
                                </span>
                                <span v-else class="text-muted-foreground">—</span>
                            </td>
                            <td class="px-4 py-2 text-muted-foreground">
                                {{ item.account.name }}
                            </td>
                            <td class="px-4 py-2">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': item.type === 'income',
                                        'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': item.type === 'expense',
                                    }"
                                >
                                    {{ t(`transactions.index.types.${item.type}`) }}
                                </span>
                            </td>
                            <td
                                class="px-4 py-2 text-right tabular-nums"
                                :class="{
                                    'text-green-600 dark:text-green-400': item.type === 'income',
                                    'text-red-600 dark:text-red-400': item.type === 'expense',
                                }"
                            >
                                {{ item.type === 'income' ? '+' : '−' }}{{ formatAmount(item.amount_cents) }}
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex justify-end gap-1">
                                    <Button variant="ghost" size="icon" class="size-7" as-child>
                                        <Link :href="editEntry({ transaction: item.id })">
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
                    </template>
                    <tr v-if="transactions.items.length === 0">
                        <td colspan="7" class="px-4 py-10 text-center text-muted-foreground">
                            {{ t('transactions.index.empty') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="transactions.meta.last_page > 1"
            class="flex items-center justify-center gap-1"
        >
            <Button
                v-for="link in transactions.meta.links"
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
