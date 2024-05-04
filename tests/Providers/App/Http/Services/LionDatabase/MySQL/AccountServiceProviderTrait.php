<?php

declare(strict_types=1);

namespace Tests\Providers\App\Http\Services\LionDatabase\MySQL;

use Database\Class\LionDatabase\MySQL\Users;

trait AccountServiceProviderTrait
{
    public static function verifyRecoveryCodeProvider(): array
    {
        return [
            [
                'users' => new Users(),
                'data' => success(),
                'exceptionMessage' => 'verification code is invalid [ERR-1]'
            ],
            [
                'users' => (new Users())->setUsersRecoveryCode(fake()->numerify('######')),
                'data' => (object) ['users_recovery_code' => fake()->numerify('######')],
                'exceptionMessage' => 'verification code is invalid [ERR-2]'
            ],
        ];
    }

    public static function verifyActivationCodeProvider(): array
    {
        return [
            [
                'users' => new Users(),
                'data' => success(),
                'exceptionMessage' => 'activation code is invalid [ERR-1]'
            ],
            [
                'users' => (new Users())->setUsersActivationCode(fake()->numerify('######')),
                'data' => (object) ['users_activation_code' => fake()->numerify('######')],
                'exceptionMessage' => 'activation code is invalid [ERR-2]'
            ],
        ];
    }
}
