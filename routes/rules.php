<?php

declare(strict_types=1);

use App\Rules\LionDatabase\MySQL\DocumentTypes\IddocumentTypesRule;
use App\Rules\LionDatabase\MySQL\Roles\IdrolesRule;
use App\Rules\LionDatabase\MySQL\Users\UsersEmailRule;
use App\Rules\LionDatabase\MySQL\Users\UsersLastNameRule;
use App\Rules\LionDatabase\MySQL\Users\UsersNameRule;
use App\Rules\LionDatabase\MySQL\Users\UsersPasswordRule;
use Lion\Bundle\Helpers\Http\Routes;
use Lion\Route\Route;

/**
 * -----------------------------------------------------------------------------
 * Rules
 * -----------------------------------------------------------------------------
 * This is where you can register your rules for validating forms
 * -----------------------------------------------------------------------------
 **/

Routes::setRules([

    /**
     * [Routes for any HTTP protocol]
     */

    Route::ANY => [
        //
    ],

    /**
     * [Routes for the HTTP POST protocol]
     */

    Route::POST => [
        '/api/auth' => [
            UsersEmailRule::class,
            UsersPasswordRule::class,
        ],
        '/api/users' => [
            IdrolesRule::class,
            IddocumentTypesRule::class,
            UsersNameRule::class,
            UsersLastNameRule::class,
            UsersEmailRule::class,
            UsersPasswordRule::class,
        ]
    ],

    /**
     * [Routes for the HTTP GET protocol]
     */

    Route::GET => [
        //
    ],

    /**
     * [Routes for the HTTP PUT protocol]
     */

    Route::PUT => [
        '/api/users/{idusers}' => [
            IdrolesRule::class,
            IddocumentTypesRule::class,
            UsersNameRule::class,
            UsersLastNameRule::class,
            UsersEmailRule::class,
        ]
    ],

    /**
     * [Routes for the HTTP DELETE protocol]
     */

    Route::DELETE => [
        //
    ],

]);
