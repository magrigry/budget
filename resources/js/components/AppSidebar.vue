<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeftRight, LayoutGrid, Settings, Tag, Wallet } from '@lucide/vue';
import { index as accountsIndex } from '@/actions/App/Http/Controllers/AccountController';
import { index as transactionsIndex } from '@/actions/App/Http/Controllers/Transaction/TransactionController';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Transactions',
        href: transactionsIndex(),
        icon: ArrowLeftRight,
    },
    {
        title: 'Comptes',
        href: accountsIndex(),
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
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
