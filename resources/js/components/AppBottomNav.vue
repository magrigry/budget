<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeftRight, Settings, Tag, Wallet } from '@lucide/vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

const { isCurrentOrParentUrl } = useCurrentUrl();

const navItems: NavItem[] = [
    {
        title: 'Transactions',
        href: { url: '/transactions', method: 'get' },
        icon: ArrowLeftRight,
    },
    {
        title: 'Comptes',
        href: { url: '/accounts', method: 'get' },
        icon: Wallet,
    },
    {
        title: 'Catégories',
        href: { url: '/categories', method: 'get' },
        icon: Tag,
    },
    {
        title: 'Paramètres',
        href: { url: '/settings/profile', method: 'get' },
        icon: Settings,
    },
];
</script>

<template>
    <nav class="fixed bottom-0 left-0 right-0 z-50 border-t bg-background md:hidden">
        <div class="flex h-16 items-center justify-around px-2">
            <Link
                v-for="item in navItems"
                :key="item.title"
                :href="item.href"
                class="flex flex-1 flex-col items-center gap-1 py-2 text-xs transition-colors"
                :class="isCurrentOrParentUrl(item.href) ? 'text-primary' : 'text-muted-foreground'"
            >
                <component :is="item.icon" class="size-5" />
                <span>{{ item.title }}</span>
            </Link>
        </div>
    </nav>
</template>
