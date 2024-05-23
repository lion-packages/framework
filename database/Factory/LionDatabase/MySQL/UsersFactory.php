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
     * [Users Email
     *
     * @const USERS_PASSWORD
     */
    const string USERS_EMAIL = 'root@dev.com';

    /**
     * [User password]
     *
     * @const USERS_PASSWORD
     */
    const string USERS_PASSWORD = 'lion';

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
            'users_activation_code',
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
                fake()->numerify('##########'),
                'root',
                'lion',
                fake()->userName(),
                self::USERS_EMAIL,
                $validation->passwordHash($validation->sha256(self::USERS_PASSWORD)),
                null,
                uniqid('code-')
            ],
            [
                RolesEnum::MANAGER->value,
                DocumentTypesEnum::CITIZENSHIP_CARD->value,
                fake()->numerify('##########'),
                'root',
                'manager',
                fake()->userName(),
                'manager@dev.com',
                $validation->passwordHash($validation->sha256(self::USERS_PASSWORD)),
                "123456",
                uniqid('code-')
            ]
        ];
    }
}
