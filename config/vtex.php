<?php

    return [
        'accountName' => '',
        'environment' => 'vtexcommercestable',
        'app_key' => '',
        'app_token' => '',
        'payment_system' => [],
        'url_og' => '',
        'master_domain' => '',
        'store_domain' => '',
        'order' => [
            'payment_system_key' => 'paymentData.transactions.0.payments.0.paymentSystem',
            'currency_key' => 'storePreferencesData.currencyCode',
        ],
    ];
