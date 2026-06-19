import { useBrowserLocale } from '@/composables/useBrowserLocale';

export function formatDate(date: string): string {
    return new Intl.DateTimeFormat(useBrowserLocale(), {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(date));
}
