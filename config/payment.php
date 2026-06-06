<?php

return [
    /**
     * Payment Methods Available dari Tripay API
     * Berdasarkan response dari /merchant/payment-method
     */
    'methods' => [
        // Virtual Account
        'BCAVA' => [
            'code' => 'BCAVA',
            'name' => 'BCA Virtual Account',
            'group' => 'Virtual Account',
            'type' => 'direct',
        ],
        'BRIVA' => [
            'code' => 'BRIVA',
            'name' => 'BRI Virtual Account',
            'group' => 'Virtual Account',
            'type' => 'direct',
        ],
        'BNIVA' => [
            'code' => 'BNIVA',
            'name' => 'BNI Virtual Account',
            'group' => 'Virtual Account',
            'type' => 'direct',
        ],
        'MANDIRIVA' => [
            'code' => 'MANDIRIVA',
            'name' => 'Mandiri Virtual Account',
            'group' => 'Virtual Account',
            'type' => 'direct',
        ],

        // Convenience Store
        'ALFACART' => [
            'code' => 'ALFACART',
            'name' => 'Alfamart / Alfacart',
            'group' => 'Convenience Store',
            'type' => 'direct',
        ],
        'INDOMARET' => [
            'code' => 'INDOMARET',
            'name' => 'Indomaret',
            'group' => 'Convenience Store',
            'type' => 'direct',
        ],

        // E-Wallet / QRIS
        'QRIS' => [
            'code' => 'QRIS',
            'name' => 'QRIS by ShopeePay',
            'group' => 'E-Wallet',
            'type' => 'direct',
        ],

        // E-Wallet
        'OVO' => [
            'code' => 'OVO',
            'name' => 'OVO',
            'group' => 'E-Wallet',
            'type' => 'redirect',
        ],
        'DANA' => [
            'code' => 'DANA',
            'name' => 'Dana',
            'group' => 'E-Wallet',
            'type' => 'redirect',
        ],
        'LINKAJA' => [
            'code' => 'LINKAJA',
            'name' => 'LinkAja',
            'group' => 'E-Wallet',
            'type' => 'redirect',
        ],

        // Credit Card
        'CREDITCARD' => [
            'code' => 'CREDITCARD',
            'name' => 'Kartu Kredit/Debit',
            'group' => 'Credit Card',
            'type' => 'redirect',
        ],
    ],

    /**
     * Grouped methods untuk display di UI
     */
    'grouped_methods' => [
        'Virtual Account' => [
            'BCAVA', 'BRIVA', 'BNIVA', 'MANDIRIVA'
        ],
        'Convenience Store' => [
            'ALFACART', 'INDOMARET'
        ],
        'E-Wallet' => [
            'QRIS', 'OVO', 'DANA', 'LINKAJA'
        ],
        'Credit Card' => [
            'CREDITCARD'
        ],
    ],

    /**
     * Bank Account Details untuk fallback manual payment
     */
    'bank' => [
        'bca' => [
            'name' => 'Bank Central Asia',
            'account' => env('BANK_BCA_ACCOUNT', ''),
            'account_name' => env('BANK_ACCOUNT_NAME', ''),
        ],
        'bri' => [
            'name' => 'Bank Rakyat Indonesia',
            'account' => env('BANK_BRI_ACCOUNT', ''),
            'account_name' => env('BANK_ACCOUNT_NAME', ''),
        ],
        'mandiri' => [
            'name' => 'Bank Mandiri',
            'account' => env('BANK_MANDIRI_ACCOUNT', ''),
            'account_name' => env('BANK_ACCOUNT_NAME', ''),
        ],
    ],
];

