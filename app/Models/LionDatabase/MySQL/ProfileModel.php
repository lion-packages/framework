<?php

declare(strict_types=1);

namespace App\Models\LionDatabase\MySQL;

use Database\Class\LionDatabase\MySQL\Users;
use Lion\Database\Drivers\MySQL as DB;

/**
 * Model for user profile data
 *
 * @package App\Models\LionDatabase\MySQL
 */
class ProfileModel
{
    /**
     * Get profile data from the database
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return array|object
     */
    public function readProfileDB(Users $users): array|object
    {
        return DB::view('read_users_by_id')
            ->select(
                'idusers',
                'idroles',
                'iddocument_types',
                'users_citizen_identification',
                'users_name',
                'users_last_name',
                'users_nickname',
                'users_email',
            )
            ->where()->equalTo('idusers', $users->getIdusers())
            ->get();
    }

    /**
     * Description of 'updateProfileDB'
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return object
     */
    public function updateProfileDB(Users $users): object
    {
        return DB::call('update_profile', [
            $users->getIddocumentTypes(),
            $users->getUsersCitizenIdentification(),
            $users->getUsersName(),
            $users->getUsersLastName(),
            $users->getUsersNickname(),
            $users->getIdusers(),
        ])->execute();
    }

    /**
     * Description of 'deleteProfileDB'
     *
     * @return object
     */
    public function deleteProfileDB(): object
    {
        return DB::call('', [])->execute();
    }
}
