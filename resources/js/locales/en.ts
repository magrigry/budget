export default {
    transactions: {
        index: {
            title: 'Transactions',
            new: 'New transaction',
            seeTransfers: 'Transfers',
            empty: 'No transactions found.',
            filters: {
                account: 'Account',
                allAccounts: 'All accounts',
                type: 'Type',
                allTypes: 'All types',
                dateFrom: 'From',
                dateTo: 'To',
                search: 'Search by label',
                reset: 'Reset',
            },
            columns: {
                date: 'Date',
                label: 'Label',
                account: 'Account',
                type: 'Type',
                amount: 'Amount',
                actions: '',
            },
            types: {
                income: 'Income',
                expense: 'Expense',
                transfer: 'Transfer',
            },
            sort: {
                date: 'Date',
                amount: 'Amount',
            },
        },
        create: {
            title: 'New transaction',
            submit: 'Create transaction',
        },
        edit: {
            title: 'Edit transaction',
            submit: 'Save changes',
        },
        transfers: {
            index: {
                title: 'Transfers',
                new: 'New transfer',
                seeEntries: 'Transactions',
                empty: 'No transfers found.',
                columns: {
                    from: 'From',
                    to: 'To',
                },
            },
            create: {
                title: 'New transfer',
                submit: 'Create transfer',
            },
            edit: {
                title: 'Edit transfer',
                submit: 'Save changes',
            },
        },
        form: {
            type: 'Type',
            income: 'Income',
            expense: 'Expense',
            transfer: 'Transfer',
            account: 'Account',
            fromAccount: 'From account',
            toAccount: 'To account',
            amount: 'Amount',
            label: 'Label',
            labelPlaceholder: 'e.g. Grocery shopping',
            date: 'Date',
            cancel: 'Cancel',
            save: 'Save',
        },
    },
    accounts: {
        index: {
            title: 'Accounts',
            new: 'New account',
            empty: 'No accounts. Create one to get started.',
            emptyArchived: 'No archived accounts.',
            showArchived: 'Show archived',
            hideArchived: 'Hide archived',
        },
        create: {
            title: 'New account',
            submit: 'Create account',
        },
        edit: {
            title: 'Edit account',
            submit: 'Save changes',
        },
        show: {
            edit: 'Edit',
            balance: 'Current balance',
            transactions: 'Transactions',
            transactionsSoon: 'Transactions will be available soon.',
        },
        form: {
            name: 'Account name',
            namePlaceholder: 'e.g. Checking account',
            currency: 'Currency',
            initialBalance: 'Initial balance',
            initialBalanceLocked:
                'The initial balance cannot be changed after creation.',
            color: 'Color',
            colorOptional: '(optional)',
            colorPlaceholder: '#3b82f6',
            cancel: 'Cancel',
            save: 'Save',
        },
    },
};
