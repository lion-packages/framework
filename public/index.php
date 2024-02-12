<?php

session_start();
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

use App\Http\HttpKernel;
use Dotenv\Dotenv;
use Lion\Bundle\Helpers\Http\Routes;
use Lion\Database\Driver;
use Lion\Request\Request;
use Lion\Route\Route;
use Lion\Security\RSA;

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
 * Import route for RSA
 * -----------------------------------------------------------------------------
 * Load default route for RSA
 * -----------------------------------------------------------------------------
 **/

if ('' != env->RSA_URL_PATH) {
    (new RSA)->setUrlPath(storage_path(env->RSA_URL_PATH));
}

/**
 * -----------------------------------------------------------------------------
 * Cross-Origin Resource Sharing (CORS) Configuration
 * -----------------------------------------------------------------------------
 * Here you can configure your settings for cross-origin resource
 * sharing or "CORS". This determines which cross-origin operations
 * can be executed in web browsers.
 * -----------------------------------------------------------------------------
 **/

foreach (require_once(__DIR__ . '/../config/cors.php') as $header => $value) {
    (new Request)->header($header, $value);
}

/**
 * -----------------------------------------------------------------------------
 * Start database service
 * -----------------------------------------------------------------------------
 * Upload data to establish a connection
 * -----------------------------------------------------------------------------
 **/


$responseDatabase = Driver::run(require_once(__DIR__ . '/../config/database.php'));

if (isError($responseDatabase)) {
    logger($responseDatabase->message, 'error', (array) $responseDatabase);
    finish(error($responseDatabase->message));
}

/**
 * -----------------------------------------------------------------------------
 * Start email sending service
 * -----------------------------------------------------------------------------
 * enter account access credentials
 * -----------------------------------------------------------------------------
 **/

// require_once("../config/email.php")

/**
 * -----------------------------------------------------------------------------
 * Use rules by routes
 * -----------------------------------------------------------------------------
 * use whatever rules you want to validate input data
 * -----------------------------------------------------------------------------
 **/

require_once(__DIR__ . '/../routes/rules.php');
$allRules = Routes::getRules();

if (isset($allRules[$_SERVER['REQUEST_METHOD']])) {
    $httpKernel = new HttpKernel();

    foreach ($allRules[$_SERVER['REQUEST_METHOD']] as $uri => $rules) {
        if ($httpKernel->checkUrl($uri)) {
            foreach ($rules as $key => $rule) {
                $ruleClass = new $rule();

                $ruleClass->passes();
                $ruleClass->display();
            }
        }
    }
}

/**
 * -----------------------------------------------------------------------------
 * Web Routes
 * -----------------------------------------------------------------------------
 * Here is where you can register web routes for your application
 * -----------------------------------------------------------------------------
 **/

date_default_timezone_set(env->SERVER_DATE_TIMEZONE);
require_once(__DIR__ . '/../routes/middleware.php');

Route::init();
Route::addMiddleware(Routes::getMiddleware());
include_once(__DIR__ . '/../routes/web.php');
Route::get('route-list', fn() => Route::getFullRoutes());
Route::dispatch();
