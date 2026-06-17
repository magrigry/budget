import { describe, expect, it, vi } from 'vitest';
import { formatCurrency } from '@/composables/useFormatCurrency';

vi.mock('@inertiajs/vue3', () => ({
    usePage: () => ({ props: { locale: 'fr' } }),
}));

describe('formatCurrency', () => {
    it('utilise la locale des props Inertia', () => {
        expect(formatCurrency(10000, 'EUR')).toBe('100,00 €');
    });

    it("fonctionne en dehors d'un composant Vue", () => {
        expect(() => formatCurrency(10000, 'EUR')).not.toThrow();
    });
});
