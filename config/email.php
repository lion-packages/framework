<?php

use Lion\Mailer\Mailer;

/**
 * -----------------------------------------------------------------------------
 * Start mail service
 * -----------------------------------------------------------------------------
 * describe connections to establish connecting to multiple databases
 * -----------------------------------------------------------------------------
 **/

Mailer::initialize([
    env->MAIL_NAME => [
        'name' => env->MAIL_NAME,
        'type' => env->MAIL_TYPE,
        'host' => env->MAIL_HOST,
        'username' => env->MAIL_USER_NAME,
        'password' => env->MAIL_PASSWORD,
        'port' => (int) env->MAIL_PORT,
        'encryption' => env->MAIL_ENCRYPTION,
        'debug' => (bool) env->MAIL_DEBUG
    ]
], env->MAIL_NAME);
