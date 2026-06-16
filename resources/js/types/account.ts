export interface Account {
    id: number;
    name: string;
    currency: string;
    initial_balance_cents: number;
    balance_cents: number;
    color: string | null;
    icon: string | null;
    archived?: boolean;
}
