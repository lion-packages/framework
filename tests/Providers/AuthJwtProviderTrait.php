<?php

declare(strict_types=1);

namespace Tests\Providers;

use Lion\Security\JWT;
use Lion\Security\RSA;

trait AuthJwtProviderTrait
{
    const AVAILABLE_USERS = 2;
    const REMAINING_USERS = 1;

    private function getAuthorization(): string
    {
        $token = (new JWT)
            ->config([
                'privateKey' => (new RSA())
                    ->setUrlPath(storage_path(env('RSA_URL_PATH'), false))
                    ->init()
                    ->getPrivateKey()
            ])
            ->encode([
                'session' => true,
            ], (int) env('JWT_EXP', 3600))
            ->get();

        return "Bearer {$token}";
    }
}
