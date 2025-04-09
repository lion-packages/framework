<?php

/**
 * -----------------------------------------------------------------------------
 * Web middleware
 * -----------------------------------------------------------------------------
 * This is where you can register web middleware for your application
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

use Lion\Bundle\Middleware\HttpsMiddleware;
use Lion\Bundle\Middleware\RouteMiddleware;
use Lion\Bundle\Support\Http\Routes;

Routes::setMiddleware([

    /**
     * [Protects the route which provides the list of available routes]
     */

    'protect-route-list' => RouteMiddleware::class,

    /**
     * [Filters that request via HTTPS]
     */
    'https' => HttpsMiddleware::class,

]);
