<?php

declare(strict_types=1);

namespace App\Models\LionDatabase\MySQL;

use Database\Class\PasswordManager;
use Lion\Database\Drivers\MySQL as DB;

/**
 * Password management model
 *
 * @package App\Models\LionDatabase\MySQL
 */
class PasswordManagerModel
{
    /**
     * Query a user's password in the database
     *
     * @param PasswordManager $passwordManager [Capsule for the
     * 'PasswordManager' entity]
     *
     * @return array|object
     */
    public function getPasswordDB(PasswordManager $passwordManager): array|object
    {
        return DB::table('users')
            ->select('users_password')
            ->where()->equalTo('idusers', $passwordManager->getIdusers())
            ->get();
    }

    /**
     * Update a user's password
     *
     * @param PasswordManager $passwordManager [Capsule for the
     * 'PasswordManager' entity]
     *
     * @return object
     */
    public function updatePasswordDB(PasswordManager $passwordManager): object
    {
        return DB::call('update_password', [
            $passwordManager->getUsersPasswordConfirm(),
            $passwordManager->getIdusers(),
        ])->execute();
    }
}
