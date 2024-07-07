<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Exceptions\PasswordException;
use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use App\Http\Services\LionDatabase\MySQL\AuthenticatorService;
use App\Models\LionDatabase\MySQL\AuthenticatorModel;
use App\Rules\LionDatabase\MySQL\Users\UsersPasswordRule;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Database\Interface\DatabaseCapsuleInterface;
use Lion\Route\Attributes\Rules;
use stdClass;

/**
 * Manage the user authentication process through 2FA
 *
 * @package App\Http\Controllers\LionDatabase\MySQL
 */
class AuthenticatorController
{
    /**
     * Check if the user's password is valid
     *
     * @route /api/profile/password/verify
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param AuthenticatorService $authenticatorService [Manage 2FA services]
     * @param AESService $aESService [Encrypt and decrypt data with AES]
     *
     * @return stdClass
     *
     * @throws PasswordException [If the user's password is incorrect]
     */
    #[Rules(UsersPasswordRule::class)]
    public function passwordVerify(
        Users $users,
        AuthenticatorService $authenticatorService,
        JWTService $jWTService,
        AESService $aESService
    ): stdClass {
        $users->capsule();

        $data = $jWTService->getTokenData(env('RSA_URL_PATH'));

        $decode = $aESService->decode([
            'idusers' => $data->idusers,
            'users_password' => $users->getUsersPassword(),
        ]);

        $authenticatorService->passwordVerify(
            $users
                ->setIdusers((int) $decode['idusers'])
                ->setUsersPassword($decode['users_password'])
        );

        return success('the password is valid');
    }

    /**
     * Enable user authentication via 2FA
     *
     * @param AuthenticatorModel $authenticatorModel [Perform queries to
     * validate user authentication through 2FA]
     *
     * @return stdClass|array|DatabaseCapsuleInterface
     */
    public function enable2FA(Users $users, AuthenticatorModel $authenticatorModel): stdClass|array|DatabaseCapsuleInterface
    {
        return $authenticatorModel->readUsersPasswordDB($users->capsule());
    }
}
