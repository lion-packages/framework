<?php

declare(strict_types=1);

define('LION_START', microtime(true));

define('IS_INDEX', false);

/**
 * -----------------------------------------------------------------------------
 * Register The Auto Loader
 * -----------------------------------------------------------------------------
 * Composer provides a convenient, automatically generated class loader for
 * this application
 * -----------------------------------------------------------------------------
 */

require_once(__DIR__ . '/../vendor/autoload.php');

use Dotenv\Dotenv;
use Lion\Files\Store;

/**
 * -----------------------------------------------------------------------------
 * Register environment variable loader automatically
 * -----------------------------------------------------------------------------
 * .dotenv provides an easy way to access environment variables with $_ENV
 * -----------------------------------------------------------------------------
 */

if (isSuccess((new Store())->exist(__DIR__ . '/../.env'))) {
    Dotenv::createMutable(__DIR__ . '/../')->load();
}

/**
 * -----------------------------------------------------------------------------
 * Database initialization
 * -----------------------------------------------------------------------------
 */

include_once(__DIR__ . '/../config/database.php');

/**
 * -----------------------------------------------------------------------------
 * Email initialization
 * -----------------------------------------------------------------------------
 */

include_once(__DIR__ . '/../config/email.php');

/**
 * -----------------------------------------------------------------------------
 * Local zone configuration
 * -----------------------------------------------------------------------------
 */

date_default_timezone_set(env('SERVER_DATE_TIMEZONE', 'America/Bogota'));
