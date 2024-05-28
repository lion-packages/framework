<?php

declare(strict_types=1);

namespace Tests\Providers;

use App\Http\Services\AESService;
use Lion\Bundle\Helpers\Commands\ProcessCommand;
use Lion\Security\AES;
use Lion\Security\JWT;
use Lion\Security\RSA;

trait AuthJwtProviderTrait
{
    const int AVAILABLE_USERS = 2;
    const int REMAINING_USERS = 1;

    private function generateKeys(string $path): void
    {
        ProcessCommand::run("php lion new:rsa --path keys/{$path}/", false);
    }

    private function AESEncode(array $rows): array
    {
        return (new AESService())
            ->setAES(new AES())
            ->encode($rows);
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

    private function getCustomAuthorization(string $path, array $data = []): string
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
