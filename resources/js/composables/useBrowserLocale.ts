import { usePage } from '@inertiajs/vue3';

export function useBrowserLocale(): string {
    return usePage().props.locale;
}
