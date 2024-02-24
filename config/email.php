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
    $_ENV['MAIL_NAME'] => [
        'name' => $_ENV['MAIL_NAME'],
        'type' => $_ENV['MAIL_TYPE'],
        'host' => $_ENV['MAIL_HOST'],
        'username' => $_ENV['MAIL_USER_NAME'],
        'password' => $_ENV['MAIL_PASSWORD'],
        'port' => (int) $_ENV['MAIL_PORT'],
        'encryption' => $_ENV['MAIL_ENCRYPTION'],
        'debug' => (bool) $_ENV['MAIL_DEBUG']
    ]
], $_ENV['MAIL_NAME']);
