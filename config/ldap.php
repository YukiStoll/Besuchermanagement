<?php

return [

    'logging' => env('LDAP_LOGGING', true),

    'connections' => [

        'default' => [

            'auto_connect' => env('ADLDAP_AUTO_CONNECT', true),

            'connection' => Adldap\Connections\Ldap::class,

            'settings' => [

                'schema' => Adldap\Schemas\ActiveDirectory::class,

                'account_prefix' => env('LDAP_ACCOUNT_PREFIX', ''),

                'account_suffix' => env('LDAP_ACCOUNT_SUFFIX', ''),

                'hosts' => explode(' ', env('ADLDAP_CONTROLLERS', 'default.com')),

                'port' => env('ADLDAP_PORT', 389),

                'timeout' => env('ADLDAP_TIMEOUT', 5),

                'base_dn' => env('ADLDAP_BASEDN', 'dc=Default,dc=com'),

                'username' => env('ADLDAP_ADMIN_USERNAME', 'default_user'),
                'password' => env('ADLDAP_ADMIN_PASSWORD', 'default_password'),

                'follow_referrals' => false,

                'use_ssl' => env('ADLDAP_USE_SSL', false),
                'use_tls' => env('ADLDAP_USE_TLS', false),

            ],

        ],

    ],

];
