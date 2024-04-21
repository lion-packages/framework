<?php

declare(strict_types=1);

namespace Tests\Providers\App\Http\Services\LionDatabase\MySQL;

use Database\Class\LionDatabase\MySQL\Users;

trait RegistrationServiceProviderTrait
{
    public static function verifyAccountProvider(): array
    {
        return [
            [
                'message' => 'verification code is invalid [ERR-1]',
                'data' => success(),
                'users' => new Users()
            ],
            [
                'message' => 'verification code is invalid [ERR-2]',
                'data' => (object) [
                    'users_activation_code' => fake()->numerify('######')
                ],
                'users' => (new Users())->setUsersActivationCode(fake()->numerify('######'))
            ],
        ];
    }
}
