<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';
</script>

<template>
    <Head title="Bienvenue" />

    <div
        class="flex min-h-screen flex-col items-center justify-center bg-background px-6 text-foreground"
    >
        <div
            class="flex w-full max-w-md flex-col items-center gap-8 text-center"
        >
            <div class="flex flex-col items-center gap-4">
                <AppLogoIcon class="size-14 text-primary" />
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Budget</h1>
                    <p class="mt-2 text-muted-foreground">
                        Suivez vos revenus, dépenses et atteignez vos objectifs
                        financiers.
                    </p>
                </div>
            </div>

            <div class="flex w-full flex-col gap-3">
                <template v-if="$page.props.auth.user">
                    <Button as-child size="lg">
                        <Link :href="dashboard()"
                            >Accéder au tableau de bord</Link
                        >
                    </Button>
                </template>
                <template v-else>
                    <Button as-child size="lg">
                        <Link :href="register()">Créer un compte</Link>
                    </Button>
                    <Button as-child variant="outline" size="lg">
                        <Link :href="login()">Se connecter</Link>
                    </Button>
                </template>
            </div>
        </div>
    </div>
</template>
