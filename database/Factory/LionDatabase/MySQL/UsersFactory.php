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
    public const string USERS_EMAIL = 'root@dev.com';

    /**
     * [User password]
     *
     * @const USERS_PASSWORD
     */
    public const string USERS_PASSWORD = 'lion';

    /**
     * [Defines whether a user has 2-step security enabled with 2FA]
     *
     * @const ENABLE_2FA
     */
    public const int ENABLED_2FA = 1;

    /**
     * [Defines whether a user has 2-step security disabled with 2FA]
     *
     * @const DISABLED_2FA
     */
    public const int DISABLED_2FA = 0;

    /**
     * {@inheritdoc}
     */
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
            'users_code',
            'users_2fa',
        ];
    }

    /**
     * {@inheritdoc}
     */
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
                $validation->passwordHash(self::USERS_PASSWORD),
                null,
                uniqid('code-'),
                self::DISABLED_2FA,
            ],
            [
                RolesEnum::MANAGER->value,
                DocumentTypesEnum::CITIZENSHIP_CARD->value,
                fake()->numerify('##########'),
                'root',
                'manager',
                fake()->userName(),
                'manager@dev.com',
                $validation->passwordHash(self::USERS_PASSWORD),
                "123456",
                uniqid('code-'),
                self::DISABLED_2FA,
            ],
            // ...array_map(function () use ($validation): array {
            //     return [
            //         RolesEnum::CUSTOMER->value,
            //         DocumentTypesEnum::CITIZENSHIP_CARD->value,
            //         fake()->numerify('##########'),
            //         fake()->userName(),
            //         fake()->lastName(),
            //         fake()->userName(),
            //         fake()->email(),
            //         $validation->passwordHash(self::USERS_PASSWORD),
            //         null,
            //         uniqid('code-'),
            //         self::DISABLED_2FA,
            //     ];
            // }, range(0, 999)),
        ];
    }
}
