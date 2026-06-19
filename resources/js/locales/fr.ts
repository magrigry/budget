export default {
    transactions: {
        index: {
            title: 'Transactions',
            new: 'Nouvelle transaction',
            seeTransfers: 'Virements',
            empty: 'Aucune transaction trouvée.',
            filters: {
                account: 'Compte',
                allAccounts: 'Tous les comptes',
                type: 'Type',
                allTypes: 'Tous les types',
                dateFrom: 'Du',
                dateTo: 'Au',
                search: 'Rechercher par libellé',
                reset: 'Réinitialiser',
            },
            columns: {
                date: 'Date',
                label: 'Libellé',
                account: 'Compte',
                type: 'Type',
                amount: 'Montant',
                actions: '',
            },
            types: {
                income: 'Revenu',
                expense: 'Dépense',
                transfer: 'Virement',
            },
            groupBy: {
                none: 'Regrouper par…',
                day: 'Jour',
                week: 'Semaine',
                month: 'Mois',
                year: 'Année',
                weekLabel: 'Semaine',
            },
            sort: {
                date: 'Date',
                amount: 'Montant',
            },
        },
        create: {
            title: 'Nouvelle transaction',
            submit: 'Créer la transaction',
        },
        edit: {
            title: 'Modifier la transaction',
            submit: 'Enregistrer les modifications',
        },
        transfers: {
            index: {
                title: 'Virements',
                new: 'Nouveau virement',
                seeEntries: 'Transactions',
                empty: 'Aucun virement trouvé.',
                columns: {
                    from: 'De',
                    to: 'Vers',
                },
            },
            create: {
                title: 'Nouveau virement',
                submit: 'Créer le virement',
            },
            edit: {
                title: 'Modifier le virement',
                submit: 'Enregistrer les modifications',
            },
        },
        form: {
            type: 'Type',
            income: 'Revenu',
            expense: 'Dépense',
            transfer: 'Virement',
            account: 'Compte',
            fromAccount: 'Compte source',
            toAccount: 'Compte destination',
            amount: 'Montant',
            label: 'Libellé',
            labelPlaceholder: 'Ex : Courses',
            date: 'Date',
            cancel: 'Annuler',
            save: 'Enregistrer',
        },
    },
    accounts: {
        index: {
            title: 'Comptes',
            new: 'Nouveau compte',
            empty: 'Aucun compte. Créez-en un pour commencer.',
            emptyArchived: 'Aucun compte archivé. Créez-en un pour commencer.',
            showArchived: 'Inclure les archivés',
            hideArchived: 'Masquer les archivés',
        },
        create: {
            title: 'Nouveau compte',
            submit: 'Créer le compte',
        },
        edit: {
            title: 'Modifier le compte',
            submit: 'Enregistrer les modifications',
        },
        show: {
            edit: 'Modifier',
            balance: 'Solde actuel',
            transactions: 'Transactions',
            transactionsSoon:
                'Les transactions seront disponibles prochainement.',
        },
        form: {
            name: 'Nom du compte',
            namePlaceholder: 'Ex : Compte courant',
            currency: 'Devise',
            initialBalance: 'Solde initial',
            initialBalanceLocked:
                'Le solde initial ne peut pas être modifié après création.',
            color: 'Couleur',
            colorOptional: '(optionnel)',
            colorPlaceholder: '#3b82f6',
            cancel: 'Annuler',
            save: 'Enregistrer',
        },
    },
};
