<?php

declare(strict_types=1);

namespace App\Models\LionDatabase\MySQL;

use Database\Class\LionDatabase\MySQL\Users;
use Lion\Database\Drivers\MySQL as DB;
use Lion\Database\Interface\DatabaseCapsuleInterface;
use PDO;
use stdClass;

/**
 * Perform queries to validate user authentication through 2FA
 *
 * @package App\Models\LionDatabase\MySQL
 */
class AuthenticatorModel
{
    /**
     * Query user password by ID
     *
     * @return stdClass|array|DatabaseCapsuleInterface
     */
    public function readUsersPasswordDB(Users $users): stdClass|array|DatabaseCapsuleInterface
    {
        return DB::table('users')
            ->select('users_password')
            ->where()->equalTo('idusers', $users->getIdusers())
            ->fetchMode(PDO::FETCH_CLASS, Users::class)
            ->get();
    }
}
