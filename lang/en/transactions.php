<?php

return [
    'index' => [
        'title' => 'Transactions',
        'new' => 'New transaction',
        'seeTransfers' => 'See transfers',
        'empty' => 'No transactions.',
        'columns' => [
            'date' => 'Date',
            'label' => 'Label',
            'account' => 'Account',
            'type' => 'Type',
            'amount' => 'Amount',
        ],
        'types' => [
            'income' => 'Income',
            'expense' => 'Expense',
        ],
        'filters' => [
            'allAccounts' => 'All accounts',
            'allTypes' => 'All types',
            'search' => 'Search…',
            'reset' => 'Reset',
        ],
        'groupBy' => [
            'label' => 'Group by',
            'none' => 'None',
            'day' => 'Day',
            'week' => 'Week',
            'month' => 'Month',
            'year' => 'Year',
            'weekLabel' => 'Week',
        ],
        'periodTotal' => 'Period balance',
    ],
    'flash' => [
        'created' => 'Transaction created.',
        'updated' => 'Transaction updated.',
        'deleted' => 'Transaction deleted.',
    ],
];
