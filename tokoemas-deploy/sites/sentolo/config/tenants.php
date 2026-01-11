<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Multi-Tenant / Multi-Store Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk mengelola multiple store/domain dalam satu codebase.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Domain to Store Mapping
    |--------------------------------------------------------------------------
    |
    | Mapping dari domain/host ke store_code.
    | Bisa juga menggunakan STORE_CODE dari .env sebagai fallback.
    |
    */
    'domains' => [
        'wates.hartowiyono.my.id' => 'wates',
        'wates1.hartowiyono.my.id' => 'wates1',
        'sentolo.hartowiyono.my.id' => 'sentolo',
        'sentolo1.hartowiyono.my.id' => 'sentolo1',
        
        // Local development
        'wates.localhost' => 'wates',
        'wates1.localhost' => 'wates1',
        'sentolo.localhost' => 'sentolo',
        'sentolo1.localhost' => 'sentolo1',
        'localhost' => env('STORE_CODE', 'wates'),
        '127.0.0.1' => env('STORE_CODE', 'wates'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Store Definitions
    |--------------------------------------------------------------------------
    |
    | Definisi setiap store beserta group dan DB connection-nya.
    |
    */
    'stores' => [
        'wates' => [
            'name' => 'Toko Emas Wates',
            'group' => 'wates_group',
            'db_connection' => 'wates',
        ],
        'wates1' => [
            'name' => 'Toko Emas Wates 1',
            'group' => 'wates_group',
            'db_connection' => 'wates',  // Same DB as wates
        ],
        'sentolo' => [
            'name' => 'Toko Emas Sentolo',
            'group' => 'sentolo_group',
            'db_connection' => 'sentolo',
        ],
        'sentolo1' => [
            'name' => 'Toko Emas Sentolo 1',
            'group' => 'sentolo_group',
            'db_connection' => 'sentolo',  // Same DB as sentolo
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Store
    |--------------------------------------------------------------------------
    |
    | Store default jika domain tidak ditemukan di mapping.
    |
    */
    'default' => env('STORE_CODE', 'wates'),

    /*
    |--------------------------------------------------------------------------
    | Member Database Connection
    |--------------------------------------------------------------------------
    |
    | Connection name untuk database member yang shared antar semua store.
    |
    */
    'member_connection' => 'member',
];
