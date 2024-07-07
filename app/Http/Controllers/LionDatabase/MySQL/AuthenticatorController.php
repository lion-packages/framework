<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Exceptions\PasswordException;
use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use App\Http\Services\LionDatabase\MySQL\AuthenticatorService;
use App\Models\LionDatabase\MySQL\UsersModel;
use App\Rules\LionDatabase\MySQL\Users\UsersPasswordRule;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Authentication\Auth2FA;
use Lion\Database\Interface\DatabaseCapsuleInterface;
use Lion\Request\Http;
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
     * @route /api/profile/2fa/qr
     *
     * @param Auth2FA $auth2FA [Provides functionality for two-factor
     * authentication (2FA) using Google Authenticator]
     * @param UsersModel $usersModel [Model for the Users entity]
     * @param AESService $aESService [Encrypt and decrypt data with AES]
     * @param JWTService $jWTService [Service to manipulate JWT tokens]
     *
     * @return stdClass|array|DatabaseCapsuleInterface
     */
    public function qr(
        Auth2FA $auth2FA,
        UsersModel $usersModel,
        AESService $aESService,
        JWTService $jWTService
    ): stdClass|array|DatabaseCapsuleInterface {
        $data = $jWTService->getTokenData(env('RSA_URL_PATH'));

        $aesDecode = $aESService->decode([
            'idusers' => $data->idusers,
        ]);

        $users = $usersModel->readUsersByIdDB(
            (new Users())
                ->setIdusers((int) $aesDecode['idusers'])
        );

        $qr2fa = $auth2FA->qr(env('APP_NAME'), $users->users_email);

        return success(null, Http::OK, (object) $aESService->encode([
            'qr' => $qr2fa->data->qr,
            'secret' => $qr2fa->data->secretKey,
        ]));
    }
}
