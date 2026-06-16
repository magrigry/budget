<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { index } from '@/actions/App/Http/Controllers/AccountController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useDetectedCurrency } from '@/composables/useDetectedCurrency';

interface AccountData {
    name: string;
    currency: string;
    initial_balance: number;
    color: string;
    icon: string;
}

const props = withDefaults(
    defineProps<{
        account?: AccountData;
        submitLabel?: string;
        lockInitialBalance?: boolean;
        onSubmit: (form: ReturnType<typeof useForm<AccountData>>) => void;
    }>(),
    {
        submitLabel: 'Enregistrer',
        lockInitialBalance: false,
    },
);

const form = useForm<AccountData>({
    name: props.account?.name ?? '',
    currency: props.account?.currency ?? useDetectedCurrency(),
    initial_balance: props.account?.initial_balance ?? 0,
    color: props.account?.color ?? '',
    icon: props.account?.icon ?? '',
});

function handleSubmit() {
    form.transform((data) => ({
        name: data.name,
        currency: data.currency,
        ...(!props.lockInitialBalance && {
            initial_balance_cents: Math.round(data.initial_balance * 100),
        }),
        color: data.color || null,
        icon: data.icon || null,
    }));
    props.onSubmit(form);
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="handleSubmit">
        <div class="space-y-2">
            <Label for="name">Nom du compte</Label>
            <Input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Ex : Compte courant"
                required
                autofocus
            />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
                <Label for="currency">Devise</Label>
                <Input
                    id="currency"
                    v-model="form.currency"
                    type="text"
                    maxlength="3"
                    placeholder="EUR"
                    class="uppercase"
                    required
                />
                <InputError :message="form.errors.currency" />
            </div>

            <div class="space-y-2">
                <Label for="initial_balance">Solde initial</Label>
                <Input
                    id="initial_balance"
                    v-model="form.initial_balance"
                    type="number"
                    step="0.01"
                    placeholder="0.00"
                    :readonly="lockInitialBalance"
                    :class="
                        lockInitialBalance
                            ? 'cursor-not-allowed opacity-60'
                            : ''
                    "
                    required
                />
                <p
                    v-if="lockInitialBalance"
                    class="text-xs text-muted-foreground"
                >
                    Le solde initial ne peut pas être modifié après création.
                </p>
                <InputError v-else :message="form.errors.initial_balance" />
            </div>
        </div>

        <div class="space-y-2">
            <Label for="color"
                >Couleur
                <span class="text-muted-foreground">(optionnel)</span></Label
            >
            <div class="flex items-center gap-3">
                <input
                    id="color"
                    v-model="form.color"
                    type="color"
                    class="h-10 w-16 cursor-pointer rounded-md border border-input bg-background p-1"
                />
                <Input
                    v-model="form.color"
                    type="text"
                    placeholder="#3b82f6"
                    maxlength="7"
                    class="font-mono"
                />
            </div>
            <InputError :message="form.errors.color" />
        </div>

        <div class="flex justify-end gap-3">
            <Button as-child variant="outline">
                <Link :href="index()">Annuler</Link>
            </Button>
            <Button type="submit" :disabled="form.processing">
                {{ submitLabel }}
            </Button>
        </div>
    </form>
</template>
