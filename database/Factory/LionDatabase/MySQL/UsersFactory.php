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
     * [Users Email for root]
     *
     * @const USERS_EMAIL
     */
    public const string USERS_EMAIL = 'root@dev.com';

    /**
     * [Users Email for Manager]
     *
     * @const USERS_EMAIL_MANAGER
     */
    public const string USERS_EMAIL_MANAGER = 'manager@dev.com';

    /**
     * [Users Email for Security]
     *
     * @const USERS_EMAIL_SECURITY
     */
    public const string USERS_EMAIL_SECURITY = 'security@dev.com';

    /**
     * [User password]
     *
     * @const USERS_PASSWORD
     */
    public const string USERS_PASSWORD = 'lion';

    /**
     * [Defines whether a user has 2-step security enabled with 2FA]
     *
     * @const ENABLED_2FA
     */
    public const int ENABLED_2FA = 1;

    /**
     * [Defines whether a user has 2-step security disabled with 2FA]
     *
     * @const DISABLED_2FA
     */
    public const int DISABLED_2FA = 0;

    /**
     * [Secret key for 2FA security]
     *
     * @const SECURITY_KEY_2FA
     */
    public const string SECURITY_KEY_2FA = 'JDKGOCESC3ZSV25S';

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
            'users_2fa_secret',
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
                null,
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
                null,
            ],
            [
                RolesEnum::MANAGER->value,
                DocumentTypesEnum::CITIZENSHIP_CARD->value,
                fake()->numerify('##########'),
                'root',
                'security',
                fake()->userName(),
                'security@dev.com',
                $validation->passwordHash(self::USERS_PASSWORD),
                null,
                uniqid('code-'),
                self::ENABLED_2FA,
                self::SECURITY_KEY_2FA,
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
            //         null,
            //     ];
            // }, range(0, 999)),
        ];
    }
}
