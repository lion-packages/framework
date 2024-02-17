<?php

use App\Http\Middleware\JWTMiddleware;
use Lion\Bundle\Helpers\Http\Routes;
use Lion\Route\Middleware;

/**
 * -----------------------------------------------------------------------------
 * Web middleware
 * -----------------------------------------------------------------------------
 * This is where you can register web middleware for your application
 * -----------------------------------------------------------------------------
 **/

Routes::setMiddleware([
    new Middleware('jwt-existence', JWTMiddleware::class, 'existence'),
    new Middleware('jwt-authorize', JWTMiddleware::class, 'authorize'),
    new Middleware('jwt-not-authorize', JWTMiddleware::class, 'notAuthorize'),
    new Middleware('jwt-without-signature', JWTMiddleware::class, 'authorizeWithoutSignature')
]);
