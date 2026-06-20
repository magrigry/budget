<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { Loader2, MapPin, MapPinOff, MapPinX } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import { index } from '@/actions/App/Http/Controllers/Transaction/TransactionController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { mapsUrl, useGeolocation } from '@/composables/useGeolocation';

const { t } = useI18n();
const { state: geoState, coordinates, requestAndFetch } = useGeolocation();

interface FormFields {
    type: App.Enums.TransactionType;
    account_id: number | '';
    category_id: number | null;
    amount: number;
    label: string;
    transacted_at: string;
    latitude: number | null;
    longitude: number | null;
}

const props = withDefaults(
    defineProps<{
        accounts: App.Data.Account[];
        categories: App.Data.Category[];
        transaction?: App.Data.Transaction.Transaction;
        submitLabel?: string;
        onSubmit: (form: ReturnType<typeof useForm<FormFields>>) => void;
    }>(),
    { submitLabel: undefined },
);

const today = new Date().toISOString().slice(0, 10);

const form = useForm<FormFields>({
    type: props.transaction?.type ?? 'expense',
    account_id: props.transaction?.account_id ?? '',
    category_id: props.transaction?.category_id ?? null,
    amount: props.transaction ? props.transaction.amount_cents / 100 : 0,
    label: props.transaction?.label ?? '',
    transacted_at: props.transaction?.transacted_at ?? today,
    latitude: props.transaction?.latitude ?? null,
    longitude: props.transaction?.longitude ?? null,
});

form.transform((data) => ({
    type: data.type,
    account_id: data.account_id,
    category_id: data.category_id || null,
    amount_cents: Math.round(data.amount * 100),
    label: data.label,
    transacted_at: data.transacted_at,
    latitude: data.latitude,
    longitude: data.longitude,
}));

async function captureLocation() {
    try {
        const coords = await requestAndFetch();
        form.latitude = coords.latitude;
        form.longitude = coords.longitude;
    } catch {
        // denied or unavailable — state already updated by composable
    }
}

function removeLocation() {
    form.latitude = null;
    form.longitude = null;
}

function handleSubmit() {
    if (
        !props.transaction &&
        geoState.value === 'granted' &&
        coordinates.value
    ) {
        form.latitude = coordinates.value.latitude;
        form.longitude = coordinates.value.longitude;
    }

    props.onSubmit(form);
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="handleSubmit">
        <!-- Type toggle -->
        <div class="space-y-2">
            <Label>{{ t('transactions.form.type') }}</Label>
            <div class="flex gap-2">
                <Button
                    type="button"
                    :variant="form.type === 'expense' ? 'default' : 'outline'"
                    size="sm"
                    @click="form.type = 'expense'"
                >
                    {{ t('transactions.form.expense') }}
                </Button>
                <Button
                    type="button"
                    :variant="form.type === 'income' ? 'default' : 'outline'"
                    size="sm"
                    @click="form.type = 'income'"
                >
                    {{ t('transactions.form.income') }}
                </Button>
            </div>
            <InputError :message="form.errors.type" />
        </div>

        <!-- Account -->
        <div class="space-y-2">
            <Label for="account_id">{{ t('transactions.form.account') }}</Label>
            <select
                id="account_id"
                v-model="form.account_id"
                required
                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none"
            >
                <option value="" disabled>—</option>
                <option v-for="a in accounts" :key="a.id" :value="a.id">
                    {{ a.name }} ({{ a.currency }})
                </option>
            </select>
            <InputError :message="form.errors.account_id" />
        </div>

        <!-- Amount + Date -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="amount">{{ t('transactions.form.amount') }}</Label>
                <Input
                    id="amount"
                    v-model="form.amount"
                    type="number"
                    step="0.01"
                    min="0.01"
                    placeholder="0.00"
                    required
                />
                <InputError :message="form.errors.amount" />
            </div>

            <div class="space-y-2">
                <Label for="transacted_at">{{
                    t('transactions.form.date')
                }}</Label>
                <Input
                    id="transacted_at"
                    v-model="form.transacted_at"
                    type="date"
                    required
                />
                <InputError :message="form.errors.transacted_at" />
            </div>
        </div>

        <!-- Category -->
        <div class="space-y-2">
            <Label for="category_id">{{
                t('transactions.form.category')
            }}</Label>
            <select
                id="category_id"
                v-model="form.category_id"
                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none"
            >
                <option :value="null">—</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">
                    {{ c.name }}
                </option>
            </select>
            <InputError :message="form.errors.category_id" />
        </div>

        <!-- Label -->
        <div class="space-y-2">
            <Label for="label">{{ t('transactions.form.label') }}</Label>
            <Input
                id="label"
                v-model="form.label"
                type="text"
                :placeholder="t('transactions.form.labelPlaceholder')"
                required
            />
            <InputError :message="form.errors.label" />
        </div>

        <!-- Geolocation -->
        <div>
            <!-- Create mode: browser permission indicator -->
            <template v-if="!transaction">
                <span
                    v-if="geoState === 'fetching'"
                    class="flex items-center gap-1.5 text-xs text-muted-foreground"
                >
                    <Loader2 class="h-3.5 w-3.5 animate-spin" />
                    {{ t('transactions.form.location.fetching') }}
                </span>
                <span
                    v-else-if="geoState === 'granted'"
                    class="flex items-center gap-1.5 text-xs text-muted-foreground"
                >
                    <MapPin class="h-3.5 w-3.5 text-green-500" />
                    {{ t('transactions.form.location.granted') }}
                </span>
                <span
                    v-else-if="geoState === 'denied'"
                    class="flex items-center gap-1.5 text-xs text-muted-foreground"
                >
                    <MapPinOff class="h-3.5 w-3.5 text-red-400" />
                    {{ t('transactions.form.location.denied') }}
                </span>
                <span
                    v-else-if="geoState === 'unavailable'"
                    class="flex items-center gap-1.5 text-xs text-muted-foreground"
                >
                    <MapPinX class="h-3.5 w-3.5 text-muted-foreground" />
                    {{ t('transactions.form.location.unavailable') }}
                </span>
                <button
                    v-else
                    type="button"
                    class="flex items-center gap-1.5 text-xs text-muted-foreground underline-offset-2 hover:text-foreground hover:underline"
                    @click="captureLocation"
                >
                    <MapPin class="h-3.5 w-3.5" />
                    {{ t('transactions.form.location.enable') }}
                </button>
            </template>

            <!-- Edit mode: manage saved location -->
            <template v-else>
                <div
                    v-if="form.latitude && form.longitude"
                    class="flex items-center gap-3"
                >
                    <a
                        :href="mapsUrl(form.latitude!, form.longitude!)"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-1.5 text-xs text-muted-foreground underline-offset-2 hover:text-foreground hover:underline"
                    >
                        <MapPin class="h-3.5 w-3.5 text-green-500" />
                        {{ t('transactions.form.location.existing') }}
                    </a>
                    <button
                        type="button"
                        class="text-xs text-muted-foreground underline-offset-2 hover:text-foreground hover:underline"
                        @click="captureLocation"
                    >
                        {{ t('transactions.form.location.recapture') }}
                    </button>
                    <button
                        type="button"
                        class="text-xs text-muted-foreground underline-offset-2 hover:text-destructive hover:underline"
                        @click="removeLocation"
                    >
                        {{ t('transactions.form.location.remove') }}
                    </button>
                </div>
                <button
                    v-else
                    type="button"
                    class="flex items-center gap-1.5 text-xs text-muted-foreground underline-offset-2 hover:text-foreground hover:underline"
                    @click="captureLocation"
                >
                    <MapPin class="h-3.5 w-3.5" />
                    {{ t('transactions.form.location.add') }}
                </button>
            </template>
        </div>

        <div class="flex justify-end gap-3">
            <Button as-child variant="outline">
                <Link :href="index()">{{ t('transactions.form.cancel') }}</Link>
            </Button>
            <Button
                type="submit"
                :disabled="form.processing || geoState === 'fetching'"
            >
                {{ submitLabel ?? t('transactions.form.save') }}
            </Button>
        </div>
    </form>
</template>
