<?php

declare(strict_types=1);

namespace Database\Factory\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use App\Enums\RolesEnum;
use Lion\Bundle\Interface\FactoryInterface;
use Lion\Security\Validation;

/**
 * Factory to generate user data by default
 *
 * @package Database\Factory\LionDatabase\MySQL
 */
class UsersFactory implements FactoryInterface
{
    /**
     * [User password]
     *
     * @const USERS_PASSWORD
     */
    const USERS_PASSWORD = 'lion';

    /**
     * {@inheritdoc}
     **/
    public static function columns(): array
    {
        return [
            'idroles',
            'iddocument_types',
            'users_citizen_identification',
            'users_name',
            'users_last_name',
            'users_nickname',
            'users_email',
            'users_password',
            'users_code'
        ];
    }

    /**
     * {@inheritdoc}
     **/
    public static function definition(): array
    {
        $validation = new Validation();

        return [
            [
                RolesEnum::ADMINISTRATOR->value,
                DocumentTypesEnum::CITIZENSHIP_CARD->value,
                null,
                'root',
                'lion',
                fake()->userName(),
                'root@dev.com',
                $validation->passwordHash($validation->sha256(self::USERS_PASSWORD)),
                uniqid('code-')
            ],
            [
                RolesEnum::MANAGER->value,
                DocumentTypesEnum::CITIZENSHIP_CARD->value,
                null,
                'root',
                'manager',
                fake()->userName(),
                'manager@dev.com',
                $validation->passwordHash($validation->sha256(self::USERS_PASSWORD)),
                uniqid('code-')
            ]
        ];
    }
}
