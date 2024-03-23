<?php

declare(strict_types=1);

use Lion\Request\Request;
use Lion\Route\Route;

/**
 * -----------------------------------------------------------------------------
 * Cross-Origin Resource Sharing (CORS) Configuration
 * -----------------------------------------------------------------------------
 * Here you can configure your settings for cross-origin resource
 * sharing or "CORS". This determines which cross-origin operations
 * can be executed in web browsers.
 * -----------------------------------------------------------------------------
 **/

Request::header('Access-Control-Allow-Origin', '*');

Request::header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');

Request::header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');

if ($_SERVER['REQUEST_METHOD'] === Route::OPTIONS) {
    http_response_code(200);

    exit;
}

Request::header('Content-Type', 'application/json; charset=UTF-8');

Request::header('Access-Control-Max-Age', '0');

Request::header('Allow', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
