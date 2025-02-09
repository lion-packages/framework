<?php

declare(strict_types=1);

use Lion\Bundle\Helpers\Http\Routes;
use Lion\Bundle\Middleware\HttpsMiddleware;
use Lion\Bundle\Middleware\RouteMiddleware;
use Lion\Route\Middleware;

/**
 * -----------------------------------------------------------------------------
 * Web middleware
 * -----------------------------------------------------------------------------
 * This is where you can register web middleware for your application
 * -----------------------------------------------------------------------------
 **/

Routes::setMiddleware([

    /**
     * [Protects the route which provides the list of available routes]
     */

    new Middleware('protect-route-list', RouteMiddleware::class, 'protectRouteList'),

    /**
     * [Filters that request via HTTPS]
     */
    new Middleware('https', HttpsMiddleware::class, 'https'),

]);
