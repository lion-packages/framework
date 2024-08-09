<?php

declare(strict_types=1);

use Lion\Database\Driver;

/**
 * -----------------------------------------------------------------------------
 * Start database service
 * -----------------------------------------------------------------------------
 * describe connections to establish connecting to multiple databases
 * -----------------------------------------------------------------------------
 **/

Driver::run([
    'default' => env('DB_NAME', 'lion_database'),
    'connections' => [
        env('DB_NAME', 'lion_database') => [
            'type' => env('DB_TYPE', 'mysql'),
            'host' => env('DB_HOST', 'mysql'),
            'port' => env('DB_PORT', 3306),
            'dbname' => env('DB_NAME', 'lion_database'),
            'user' => env('DB_USER', 'root'),
            'password' => env('DB_PASSWORD', 'lion'),
        ],
    ]
]);
