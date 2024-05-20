<?php

declare(strict_types=1);

namespace Tests\Providers;

use Lion\Bundle\Helpers\Commands\ProcessCommand;
use Lion\Security\JWT;
use Lion\Security\RSA;

trait AuthJwtProviderTrait
{
    const AVAILABLE_USERS = 2;
    const REMAINING_USERS = 1;

    private function generateKeys(string $path): void
    {
        ProcessCommand::run("php lion new:rsa --path keys/{$path}/", false);
    }

    private function getAuthorization(array $data = []): string
    {
        $token = (new JWT)
            ->config([
                'privateKey' => (new RSA())
                    ->setUrlPath(env('RSA_URL_PATH'))
                    ->init()
                    ->getPrivateKey()
            ])
            ->encode([
                'session' => true,
                ...$data
            ], (int) env('JWT_EXP', 3600))
            ->get();

        return "Bearer {$token}";
    }

    public function getCustomAuthorization(string $path, array $data = []): string
    {
        $token = (new JWT)
            ->config([
                'privateKey' => (new RSA())
                    ->setUrlPath(str->of(env('RSA_URL_PATH'))->concat($path)->get())
                    ->init()
                    ->getPrivateKey()
            ])
            ->encode([
                'session' => true,
                ...$data
            ], (int) env('JWT_EXP', 3600))
            ->get();

        return "Bearer {$token}";
    }
}
