<?php

use Dotenv\Dotenv;
use Lion\Bundle\Support\ExceptionHandler;
use Lion\Bundle\Support\Http\Routes;
use Lion\Exceptions\Serialize;
use Lion\Files\Store;
use Lion\Route\Route;

/**
 * -----------------------------------------------------------------------------
 * Initialize exception handling
 * -----------------------------------------------------------------------------
 * Controls and serializes exceptions to JSON format
 * -----------------------------------------------------------------------------
 */

include_once __DIR__ . '/../config/exception.handler.php';

new Serialize()
    ->exceptionHandler(
        callback: ExceptionHandler::getOptions()['callback'],
        addInformation: ExceptionHandler::getOptions()['addInformation']
    );

/**
 * -----------------------------------------------------------------------------
 * Register environment variable loader automatically
 * -----------------------------------------------------------------------------
 * .dotenv provides an easy way to access environment variables with $_ENV
 * -----------------------------------------------------------------------------
 */

if (isSuccess(new Store()->exist(__DIR__ . '/../.env'))) {
    Dotenv::createMutable(__DIR__ . '/../')->load();
}

/**
 * -----------------------------------------------------------------------------
 * Cross-Origin Resource Sharing (CORS) Configuration
 * -----------------------------------------------------------------------------
 * Here you can configure your settings for cross-origin resource
 * sharing or "CORS". This determines which cross-origin operations
 * can be executed in web browsers.
 * -----------------------------------------------------------------------------
 */

include_once __DIR__ . '/../config/cors.php';

/**
 * -----------------------------------------------------------------------------
 * Database initialization
 * -----------------------------------------------------------------------------
 */

include_once __DIR__ . '/../config/database.php';

/**
 * -----------------------------------------------------------------------------
 * Email initialization
 * -----------------------------------------------------------------------------
 */

include_once __DIR__ . '/../config/email.php';

/**
 * -----------------------------------------------------------------------------
 * Local zone configuration
 * -----------------------------------------------------------------------------
 */

/** @phpstan-ignore-next-line */
date_default_timezone_set(env('SERVER_DATE_TIMEZONE', 'America/Bogota'));

/**
 * -----------------------------------------------------------------------------
 * Web Routes
 * -----------------------------------------------------------------------------
 * Here is where you can register web routes for your application
 * -----------------------------------------------------------------------------
 */

Route::init();

Route::addMiddleware(Routes::getMiddleware());

include_once __DIR__ . '/../routes/web.php';

Route::get('route-list', fn () => Route::getFullRoutes(), ['protect-route-list']);

Route::dispatch();
