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
    'default' => $_ENV['DB_NAME'],
    'connections' => [
        $_ENV['DB_NAME'] => [
            'type' => $_ENV['DB_TYPE'],
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD']
        ]
    ]
]);
