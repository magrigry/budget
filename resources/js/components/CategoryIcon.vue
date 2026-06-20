<script setup lang="ts">
import {
    Baby,
    Banknote,
    Book,
    Briefcase,
    Bus,
    Car,
    CreditCard,
    Dumbbell,
    Film,
    GraduationCap,
    Heart,
    Home,
    Music,
    Plane,
    ShoppingBag,
    ShoppingCart,
    Smartphone,
    Stethoscope,
    Utensils,
    Wallet,
    Wrench,
    Zap,
    Coffee,
    Gift,
    Shirt,
    TrendingUp,
    PiggyBank,
    TreePine,
    Gamepad2,
    type LucideProps,
} from '@lucide/vue';
import { computed, type Component } from 'vue';

export const CATEGORY_ICONS: Record<string, Component> = {
    shopping_cart: ShoppingCart,
    shopping_bag: ShoppingBag,
    home: Home,
    car: Car,
    bus: Bus,
    utensils: Utensils,
    coffee: Coffee,
    plane: Plane,
    heart: Heart,
    stethoscope: Stethoscope,
    briefcase: Briefcase,
    graduation_cap: GraduationCap,
    gift: Gift,
    music: Music,
    dumbbell: Dumbbell,
    shirt: Shirt,
    smartphone: Smartphone,
    film: Film,
    book: Book,
    wallet: Wallet,
    banknote: Banknote,
    credit_card: CreditCard,
    trending_up: TrendingUp,
    piggy_bank: PiggyBank,
    zap: Zap,
    wrench: Wrench,
    baby: Baby,
    tree_pine: TreePine,
    gamepad2: Gamepad2,
};

const props = withDefaults(
    defineProps<{
        icon: string | null | undefined;
        class?: string;
    } & Partial<LucideProps>>(),
    { icon: undefined, class: 'size-4' },
);

const iconComponent = computed(() =>
    props.icon ? (CATEGORY_ICONS[props.icon] ?? null) : null,
);
</script>

<template>
    <component
        :is="iconComponent"
        v-if="iconComponent"
        :class="props.class"
    />
</template>
