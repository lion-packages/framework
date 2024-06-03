<?php

declare(strict_types=1);

namespace App\Models\LionDatabase\MySQL;

use Database\Class\LionDatabase\MySQL\Users;
use Lion\Database\Drivers\MySQL as DB;
use Lion\Database\Interface\DatabaseCapsuleInterface;
use stdClass;

/**
 * Model for the Users entity
 *
 * @package App\Models\LionDatabase\MySQL
 */
class UsersModel
{
    /**
     * Create users
     *
     * @param Users $users [Object of the Users entity]
     *
     * @return stdClass
     */
    public function createUsersDB(Users $users): stdClass
    {
        return DB::call('create_users', [
            $users->getIdroles(),
            $users->getIddocumentTypes(),
            $users->getUsersCitizenIdentification(),
            $users->getUsersName(),
            $users->getUsersLastName(),
            $users->getUsersNickname(),
            $users->getUsersEmail(),
            $users->getUsersPassword(),
            $users->getUsersActivationCode(),
            $users->getUsersRecoveryCode(),
            $users->getUsersCode(),
        ])
            ->execute();
    }

    /**
     * Read users
     *
     * @return stdClass|array|DatabaseCapsuleInterface
     */
    public function readUsersDB(): stdClass|array|DatabaseCapsuleInterface
    {
        return DB::view('read_users')
            ->select()
            ->getAll();
    }

    /**
     * Read users by id
     *
     * @param Users $users [Object of the Users entity]
     *
     * @return stdClass|array|DatabaseCapsuleInterface
     */
    public function readUsersByIdDB(Users $users): stdClass|array|DatabaseCapsuleInterface
    {
        return DB::view('read_users_by_id')
            ->select()
            ->where()->equalTo('idusers', $users->getIdusers())
            ->get();
    }

    /**
     * Read users by email
     *
     * @param Users $users [Object of the Users entity]
     *
     * @return stdClass|array|DatabaseCapsuleInterface
     */
    public function readUsersByEmailDB(Users $users): stdClass|array|DatabaseCapsuleInterface
    {
        return DB::view('read_users_by_id')
            ->select()
            ->where()->equalTo('users_email', $users->getUsersEmail())
            ->get();
    }

    /**
     * Update users
     *
     * @param Users $users [Object of the Users entity]
     *
     * @return stdClass
     */
    public function updateUsersDB(Users $users): stdClass
    {
        return DB::call('update_users', [
            $users->getIdroles(),
            $users->getIddocumentTypes(),
            $users->getUsersCitizenIdentification(),
            $users->getUsersName(),
            $users->getUsersLastName(),
            $users->getUsersNickname(),
            $users->getUsersEmail(),
            $users->getIdusers(),
        ])
            ->execute();
    }

    /**
     * Update an account activation code
     *
     * @param Users $users [Object of the Users entity]
     *
     * @return stdClass
     */
    public function updateActivationCodeDB(Users $users): stdClass
    {
        return DB::call('update_activation_code', [
            $users->getUsersActivationCode(),
            $users->getIdusers(),
        ])
            ->execute();
    }

    /**
     * Update an account recovery code
     *
     * @param Users $users [Object of the Users entity]
     *
     * @return stdClass
     */
    public function updateRecoveryCodeDB(Users $users): stdClass
    {
        return DB::call('update_recovery_code', [
            $users->getUsersRecoveryCode(),
            $users->getIdusers(),
        ])
            ->execute();
    }

    /**
     * Delete users
     *
     * @param Users $users [Object of the Users entity]
     *
     * @return stdClass
     */
    public function deleteUsersDB(Users $users): stdClass
    {
        return DB::call('delete_users', [
            $users->getIdusers(),
        ])
            ->execute();
    }
}
