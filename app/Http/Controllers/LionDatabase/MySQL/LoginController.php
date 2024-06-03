<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Exceptions\PasswordException;
use App\Http\Services\LionDatabase\MySQL\LoginService;
use App\Http\Services\LionDatabase\MySQL\PasswordManagerService;
use App\Models\LionDatabase\MySQL\LoginModel;
use App\Rules\LionDatabase\MySQL\Users\UsersEmailRule;
use App\Rules\LionDatabase\MySQL\Users\UsersPasswordRule;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Request\Http;
use Lion\Route\Attributes\Rules;
use stdClass;

/**
 * Controller for user authentication
 *
 * @package App\Http\Controllers\LionDatabase\MySQL
 */
class LoginController
{
    /**
     * Manage user authentication
     *
     * @route /api/auth/login
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param LoginModel $loginModel [Model for user authentication]
     * @param LoginService $loginService [Allows you to manage the user
     * authentication process]
     * @param PasswordManagerService $passwordManagerService [Manage different
     * processes for strong password verifications]
     *
     * @return stdClass
     *
     * @throws AuthenticationException
     * @throws PasswordException
     */
    #[Rules(
        UsersEmailRule::class,
        UsersPasswordRule::class
    )]
    public function auth(
        Users $users,
        LoginModel $loginModel,
        LoginService $loginService,
        PasswordManagerService $passwordManagerService
    ): stdClass {
        $loginService->validateSession($users->capsule());

        $loginService->verifyAccountActivation($users);

        /** @var Users $session */
        $session = $loginModel->sessionDB($users);

        $passwordManagerService->verifyPasswords(
            $session->getUsersPassword(),
            $users->getUsersPassword(),
            'email/password is incorrect [AUTH-2]'
        );

        return success('successfully authenticated user', Http::OK, [
            'full_name' => str
                ->of("{$session->getUsersName()} {$session->getUsersLastName()}")
                ->trim()
                ->toNull()
                ->get(),
            ...$loginService->generateTokens($session),
        ]);
    }
}
