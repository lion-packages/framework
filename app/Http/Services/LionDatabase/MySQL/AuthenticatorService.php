<?php

declare(strict_types=1);

namespace App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\PasswordException;
use App\Models\LionDatabase\MySQL\AuthenticatorModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Database\Interface\DatabaseCapsuleInterface;

/**
 * Manage 2FA services
 *
 * @property AuthenticatorModel $authenticatorModel [Perform queries to validate
 * user authentication through 2FA]
 *
 * @package App\Http\Services\LionDatabase\MySQL
 */
class AuthenticatorService
{
    /**
     * [Perform queries to validate user authentication through 2FA]
     *
     * @var AuthenticatorModel $authenticatorModel
     */
    private AuthenticatorModel $authenticatorModel;

    /**
     * @required
     */
    public function setAuthenticatorModel(AuthenticatorModel $authenticatorModel): AuthenticatorService
    {
        $this->authenticatorModel = $authenticatorModel;

        return $this;
    }

    /**
     * Check if the password entered is valid within the system
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     *
     * @throws PasswordException [If the user's password is incorrect]
     */
    public function passwordVerify(Users $users): void
    {
        /** @var DatabaseCapsuleInterface|Users $capsule */
        $capsule = $this->authenticatorModel->readUsersPasswordDB($users);

        if (!password_verify($users->getUsersPassword(), $capsule->getUsersPassword())) {
            throw new PasswordException('password is invalid');
        }
    }
}
