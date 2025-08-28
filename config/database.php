<?php

/**
 * -----------------------------------------------------------------------------
 * Start database service.
 * -----------------------------------------------------------------------------
 * describe connections to establish connecting to multiple databases.
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

use Lion\Database\Driver;

Driver::run([
    'default' => env('DB_DEFAULT', 'local'),
    'connections' => [
        'local' => [
            'type' => env('DB_TYPE', 'mysql'),
            'host' => env('DB_HOST', 'mysql'),
            'port' => env('DB_PORT', 3306),
            'dbname' => env('DB_NAME', 'lion_database'),
            'user' => env('DB_USER', 'root'),
            'password' => env('DB_PASSWORD', 'lion'),
        ],
    ],
]);
