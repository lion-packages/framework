<?php

/**
 * -----------------------------------------------------------------------------
 * Start mail service.
 * -----------------------------------------------------------------------------
 * describe connections to establish connecting to multiple databases.
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

use Lion\Mailer\Mailer;

Mailer::initialize([
    env('MAIL_NAME', 'lion-framework') => [
        'name' => env('MAIL_NAME', 'lion-framework'),
        'type' => env('MAIL_TYPE', 'phpmailer'),
        'host' => env('MAIL_HOST', 'mailhog'),
        'username' => env('MAIL_USER_NAME', 'lion-framework'),
        'password' => env('MAIL_PASSWORD', 'lion'),
        'port' => (int) env('MAIL_PORT', 1025),
        'encryption' => env('MAIL_ENCRYPTION', false),
        'debug' => env('MAIL_DEBUG', false),
    ],
], env('MAIL_NAME', 'lion-framework'));
