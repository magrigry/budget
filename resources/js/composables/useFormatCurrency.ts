import { useBrowserLocale } from '@/composables/useBrowserLocale';

export function formatCurrency(amountCents: number, currency: string): string {
    return new Intl.NumberFormat(useBrowserLocale(), {
        style: 'currency',
        currency,
    }).format(amountCents / 100);
}
