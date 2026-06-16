export function formatCurrency(amountCents: number, currency: string): string {
    return new Intl.NumberFormat(navigator.language, {
        style: 'currency',
        currency,
    }).format(amountCents / 100);
}
