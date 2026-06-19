<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { index } from '@/actions/App/Http/Controllers/Transaction/TransactionController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const { t } = useI18n();

interface FormFields {
    from_account_id: number | '';
    to_account_id: number | '';
    amount: number;
    label: string;
    transacted_at: string;
}

const props = withDefaults(
    defineProps<{
        accounts: App.Data.Account[];
        transfer?: App.Data.Transaction.Transfer;
        submitLabel?: string;
        onSubmit: (form: ReturnType<typeof useForm<FormFields>>) => void;
    }>(),
    { submitLabel: undefined },
);

const today = new Date().toISOString().slice(0, 10);

const form = useForm<FormFields>({
    from_account_id: props.transfer?.from_account_id ?? '',
    to_account_id: props.transfer?.to_account_id ?? '',
    amount: props.transfer ? props.transfer.amount_cents / 100 : 0,
    label: props.transfer?.label ?? '',
    transacted_at: props.transfer?.transacted_at ?? today,
});

function handleSubmit() {
    form.transform((data) => ({
        from_account_id: data.from_account_id,
        to_account_id: data.to_account_id,
        amount_cents: Math.round(data.amount * 100),
        label: data.label,
        transacted_at: data.transacted_at,
    }));
    props.onSubmit(form);
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="handleSubmit">
        <!-- From / To accounts -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="from_account_id">{{
                    t('transactions.form.fromAccount')
                }}</Label>
                <select
                    id="from_account_id"
                    v-model="form.from_account_id"
                    required
                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none"
                >
                    <option value="" disabled>—</option>
                    <option v-for="a in accounts" :key="a.id" :value="a.id">
                        {{ a.name }} ({{ a.currency }})
                    </option>
                </select>
                <InputError :message="form.errors.from_account_id" />
            </div>

            <div class="space-y-2">
                <Label for="to_account_id">{{
                    t('transactions.form.toAccount')
                }}</Label>
                <select
                    id="to_account_id"
                    v-model="form.to_account_id"
                    required
                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none"
                >
                    <option value="" disabled>—</option>
                    <option
                        v-for="a in accounts"
                        :key="a.id"
                        :value="a.id"
                        :disabled="a.id === Number(form.from_account_id)"
                    >
                        {{ a.name }} ({{ a.currency }})
                    </option>
                </select>
                <InputError :message="form.errors.to_account_id" />
            </div>
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

        <div class="flex justify-end gap-3">
            <Button as-child variant="outline">
                <Link :href="index()">{{ t('transactions.form.cancel') }}</Link>
            </Button>
            <Button type="submit" :disabled="form.processing">
                {{ submitLabel ?? t('transactions.form.save') }}
            </Button>
        </div>
    </form>
</template>
