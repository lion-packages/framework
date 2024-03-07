<?php

declare(strict_types=1);

use Lion\Mailer\Mailer;

/**
 * -----------------------------------------------------------------------------
 * Start mail service
 * -----------------------------------------------------------------------------
 * describe connections to establish connecting to multiple databases
 * -----------------------------------------------------------------------------
 **/

Mailer::initialize([
    env('MAIL_NAME', 'lion-app') => [
        'name' => env('MAIL_NAME', 'lion-app'),
        'type' => env('MAIL_TYPE', 'symfony'),
        'host' => env('MAIL_HOST', 'mailhog'),
        'username' => env('MAIL_USER_NAME', 'lion-app'),
        'password' => env('MAIL_PASSWORD', 'lion'),
        'port' => (int) env('MAIL_PORT', 1025),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'debug' => env('MAIL_DEBUG', false)
    ]
], env('MAIL_NAME', 'lion-app'));
