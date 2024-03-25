<?php

define('LION_START', microtime(true));

/**
 * -----------------------------------------------------------------------------
 * Register The Auto Loader
 * -----------------------------------------------------------------------------
 * Composer provides a convenient, automatically generated class loader for
 * this application
 * -----------------------------------------------------------------------------
 **/

require_once(__DIR__ . '/../vendor/autoload.php');

use Dotenv\Dotenv;
use Lion\Bundle\Helpers\ExceptionCore;
use Lion\Bundle\Helpers\Http\Routes;
use Lion\Bundle\HttpKernel;
use Lion\DependencyInjection\Container;
use Lion\Route\Route;

/**
 * -----------------------------------------------------------------------------
 * Initialize exception handling
 * -----------------------------------------------------------------------------
 * Controls and serializes exceptions to JSON format
 * -----------------------------------------------------------------------------
 **/

(new ExceptionCore)->exceptionHandler();

/**
 * -----------------------------------------------------------------------------
 * Register environment variable loader automatically
 * -----------------------------------------------------------------------------
 * .dotenv provides an easy way to access environment variables with $_ENV
 * -----------------------------------------------------------------------------
 **/

Dotenv::createImmutable(__DIR__ . '/../')->load();

/**
 * -----------------------------------------------------------------------------
 * Cross-Origin Resource Sharing (CORS) Configuration
 * -----------------------------------------------------------------------------
 * Here you can configure your settings for cross-origin resource
 * sharing or "CORS". This determines which cross-origin operations
 * can be executed in web browsers.
 * -----------------------------------------------------------------------------
 **/

require_once(__DIR__ . '/../config/cors.php');

/**
 * -----------------------------------------------------------------------------
 * Use rules by routes
 * -----------------------------------------------------------------------------
 * use whatever rules you want to validate input data
 * -----------------------------------------------------------------------------
 **/

(new Container)->injectDependencies((new HttpKernel))->validateRules();

/**
 * -----------------------------------------------------------------------------
 * Database initialization
 * -----------------------------------------------------------------------------
 * */

include_once(__DIR__ . '/../config/database.php');

/**
 * -----------------------------------------------------------------------------
 * Email initialization
 * -----------------------------------------------------------------------------
 * */

include_once(__DIR__ . '/../config/email.php');

/**
 * -----------------------------------------------------------------------------
 * Local zone configuration
 * -----------------------------------------------------------------------------
 */

date_default_timezone_set(env('SERVER_DATE_TIMEZONE', 'America/Bogota'));

/**
 * -----------------------------------------------------------------------------
 * Web Routes
 * -----------------------------------------------------------------------------
 * Here is where you can register web routes for your application
 * -----------------------------------------------------------------------------
 **/

Route::init();
Route::addMiddleware(Routes::getMiddleware());
include_once(__DIR__ . '/../routes/web.php');
Route::get('route-list', fn() => Route::getFullRoutes(), ['protect-route-list']);
Route::dispatch();
