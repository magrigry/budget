import { useBrowserLocale } from '@/composables/useBrowserLocale';

const regionCurrencyMap: Record<string, string> = {
    US: 'USD',
    GB: 'GBP',
    JP: 'JPY',
    CA: 'CAD',
    AU: 'AUD',
    CH: 'CHF',
    CN: 'CNY',
    IN: 'INR',
    BR: 'BRL',
    MX: 'MXN',
};

export function useDetectedCurrency(): string {
    const browserLocale = useBrowserLocale();
    const region = browserLocale.split('-')[1]?.toUpperCase() ?? '';

    return regionCurrencyMap[region] ?? 'EUR';
}
