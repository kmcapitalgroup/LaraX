<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MultiversX Network Configuration
    |--------------------------------------------------------------------------
    */
    
    'network' => env('MULTIVERSX_NETWORK', 'mainnet'),
    
    'networks' => [
        'mainnet' => [
            'api_url' => 'https://api.multiversx.com',
            'gateway_url' => 'https://gateway.multiversx.com',
        ],
        'testnet' => [
            'api_url' => 'https://testnet-api.multiversx.com',
            'gateway_url' => 'https://testnet-gateway.multiversx.com',
        ],
        'devnet' => [
            'api_url' => 'https://devnet-api.multiversx.com',
            'gateway_url' => 'https://devnet-gateway.multiversx.com',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Wallet Configuration
    |--------------------------------------------------------------------------
    */
    
    'wallet' => [
        'pem_file' => env('MULTIVERSX_WALLET_PEM'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gas Configuration
    |--------------------------------------------------------------------------
    */
    
    'gas' => [
        'default_gas_limit' => 50000,
        'default_gas_price' => 1000000000,
    ],
];
