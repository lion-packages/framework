<?php

/**
 * -----------------------------------------------------------------------------
 * Cross-Origin Resource Sharing (CORS) Configuration
 * -----------------------------------------------------------------------------
 * Here you can configure your settings for cross-origin resource
 * sharing or "CORS". This determines which cross-origin operations
 * can be executed in web browsers.
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

use Lion\Request\Http;

/** @var string $serverUrlAud */
$serverUrlAud = env('SERVER_URL_AUD', 'http://localhost:5173');

header('Access-Control-Allow-Origin: ' . $serverUrlAud);

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');

header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');

header('Access-Control-Max-Age: 3600');

if (Http::OPTIONS === $_SERVER['REQUEST_METHOD']) {
    http_response_code(Http::OK);

    exit(0);
}

header('Content-Type: application/json; charset=UTF-8');
