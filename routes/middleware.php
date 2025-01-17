<?php

declare(strict_types=1);

use App\Enums\RolesEnum;
use App\Http\Middleware\JWTMiddleware;
use App\Http\Middleware\RolesMiddleware;
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

    /**
     * [Filters to validate different states with JWT]
     */

    new Middleware('jwt-existence', JWTMiddleware::class, 'existence'),

    new Middleware('jwt-authorize', JWTMiddleware::class, 'authorize'),

    new Middleware('jwt-not-authorize', JWTMiddleware::class, 'notAuthorize'),

    new Middleware('jwt-without-signature', JWTMiddleware::class, 'authorizeWithoutSignature'),

    /**
     * Filters to validate user roles
     */

    new Middleware('admin-access', RolesMiddleware::class, 'access', [
        'roles' => [
            RolesEnum::ADMINISTRATOR->value,
        ],
    ]),

]);
