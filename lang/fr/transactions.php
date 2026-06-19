<?php

return [
    'index' => [
        'title' => 'Transactions',
        'new' => 'Nouvelle transaction',
        'seeTransfers' => 'Voir les virements',
        'empty' => 'Aucune transaction.',
        'columns' => [
            'date' => 'Date',
            'label' => 'Libellé',
            'account' => 'Compte',
            'type' => 'Type',
            'amount' => 'Montant',
        ],
        'types' => [
            'income' => 'Revenu',
            'expense' => 'Dépense',
        ],
        'filters' => [
            'allAccounts' => 'Tous les comptes',
            'allTypes' => 'Tous les types',
            'search' => 'Rechercher…',
            'reset' => 'Réinitialiser',
        ],
        'groupBy' => [
            'label' => 'Regrouper par',
            'none' => 'Aucun',
            'day' => 'Jour',
            'week' => 'Semaine',
            'month' => 'Mois',
            'year' => 'Année',
            'weekLabel' => 'Semaine',
        ],
        'periodTotal' => 'Solde de la période',
    ],
    'flash' => [
        'created' => 'Transaction créée.',
        'updated' => 'Transaction modifiée.',
        'deleted' => 'Transaction supprimée.',
    ],
];
