<?php

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
    Route::ANY => [
        //
    ],
    Route::POST => [
        '/api/users' => [
            IdrolesRule::class,
            IddocumentTypesRule::class,
            UsersNameRule::class,
            UsersLastNameRule::class,
            UsersEmailRule::class,
            UsersPasswordRule::class
        ]
    ],
    Route::GET => [
        //
    ],
    Route::PUT => [
        '/api/users/{idusers}' => [
            IdrolesRule::class,
            IddocumentTypesRule::class,
            UsersNameRule::class,
            UsersLastNameRule::class,
            UsersEmailRule::class,
        ]
    ],
    Route::DELETE => [
        //
    ],
]);
