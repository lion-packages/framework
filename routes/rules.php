<?php

declare(strict_types=1);

use App\Rules\LionDatabase\MySQL\DocumentTypes\IddocumentTypesRule;
use App\Rules\LionDatabase\MySQL\Roles\IdrolesRule;
use App\Rules\LionDatabase\MySQL\Users\UsersActivationCodeRequiredRule;
use App\Rules\LionDatabase\MySQL\Users\UsersCitizenIdentificationRequiredRule;
use App\Rules\LionDatabase\MySQL\Users\UsersEmailRule;
use App\Rules\LionDatabase\MySQL\Users\UsersLastNameRequiredRule;
use App\Rules\LionDatabase\MySQL\Users\UsersNameRequiredRule;
use App\Rules\LionDatabase\MySQL\Users\UsersNicknameRequiredRule;
use App\Rules\LionDatabase\MySQL\Users\UsersPasswordRule;
use App\Rules\LionDatabase\MySQL\Users\UsersRecoveryCodeRequiredRule;
use App\Rules\UsersPasswordConfirmRule;
use App\Rules\UsersPasswordNewRule;
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
        '/api/auth/login' => [
            UsersEmailRule::class,
            UsersPasswordRule::class,
        ],
        '/api/auth/register' => [
            UsersEmailRule::class,
            UsersPasswordRule::class,
        ],
        '/api/auth/verify' => [
            UsersActivationCodeRequiredRule::class,
            UsersEmailRule::class,
        ],
        '/api/users' => [
            IdrolesRule::class,
            IddocumentTypesRule::class,
            UsersCitizenIdentificationRequiredRule::class,
            UsersNameRequiredRule::class,
            UsersLastNameRequiredRule::class,
            UsersNicknameRequiredRule::class,
            UsersEmailRule::class,
            UsersPasswordRule::class,
        ],
        '/api/auth/password/recovery' => [
            UsersEmailRule::class,
        ],
        '/api/auth/password/verify-code' => [
            UsersEmailRule::class,
            UsersRecoveryCodeRequiredRule::class,
            UsersPasswordNewRule::class,
            UsersPasswordConfirmRule::class,
        ],
        '/api/auth/password/update' => [
            UsersPasswordRule::class,
            UsersPasswordNewRule::class,
            UsersPasswordConfirmRule::class,
        ],
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
        '/api/users/{idusers:i}' => [
            IdrolesRule::class,
            IddocumentTypesRule::class,
            UsersCitizenIdentificationRequiredRule::class,
            UsersNameRequiredRule::class,
            UsersLastNameRequiredRule::class,
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
