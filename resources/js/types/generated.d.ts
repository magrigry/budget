declare namespace App {
    namespace Data {
        export type Account = {
            readonly id: number;
            readonly name: string;
            readonly currency: string;
            readonly initial_balance_cents: number;
            readonly balance_cents: number;
            readonly color: string | null;
            readonly icon: string | null;
            readonly archived: boolean;
        };
        namespace Transaction {
            export type Transaction = {
                readonly id: number;
                readonly account_id: number;
                readonly type: App.Enums.TransactionType;
                readonly amount_cents: number;
                readonly label: string;
                readonly transacted_at: string;
                readonly account: App.Data.Account;
            };
            export type Transfer = {
                readonly id: number;
                readonly from_account_id: number;
                readonly to_account_id: number;
                readonly amount_cents: number;
                readonly label: string;
                readonly transacted_at: string;
                readonly from_account: App.Data.Account;
                readonly to_account: App.Data.Account;
            };
        }
    }
    namespace Enums {
        export type TransactionType = 'income' | 'expense';
    }
}
