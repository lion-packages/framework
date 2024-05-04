<?php

declare(strict_types=1);

namespace App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\PasswordException;
use Lion\Request\Request;

/**
 * Manage different processes for strong password verifications
 *
 * @package App\Http\Services\LionDatabase\MySQL
 */
class PasswordManagerService
{
    /**
     * Verifies a password
     *
     * @param string $usersPassword [The password provided by the user]
     * @param string $passwordEntered [The password stored in the session]
     * @param string $message [Exception message]
     *
     * @return void
     *
     * @throws PasswordException [If the passwords do not match]
     */
    public function verifyPasswords(
        string $usersPassword,
        string $passwordEntered,
        string $message = 'password is incorrect [ERR-1]'
    ): void {
        if (!password_verify($passwordEntered, $usersPassword)) {
            throw new PasswordException($message, Request::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Compare passwords
     *
     * @param string $usersPassword [The password provided by the user]
     * @param string $passwordEntered [The password stored in the session]
     *
     * @return void
     *
     * @throws PasswordException [If the passwords do not match]
     */
    public function comparePasswords(string $usersPassword, string $passwordEntered): void
    {
        if ($usersPassword != $passwordEntered) {
            throw new PasswordException('password is incorrect [ERR-2]', Request::HTTP_UNAUTHORIZED);
        }
    }
}
