<?php

declare(strict_types=1);

namespace Tests\App\Http\Middleware;

use App\Enums\RolesEnum;
use App\Http\Middleware\RolesMiddleware;
use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use Lion\Bundle\Exceptions\MiddlewareException;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\AES;
use Lion\Security\JWT;
use Lion\Security\RSA;
use Lion\Test\Test;
use Tests\Providers\AuthJwtProviderTrait;

class RolesMiddlewareTest extends Test
{
    use AuthJwtProviderTrait;

    private RolesMiddleware $rolesMiddleware;

    protected function setUp(): void
    {
        $this->rolesMiddleware = (new RolesMiddleware())
            ->setAESService(
                (new AESService())
                    ->setAES(new AES())
            )
            ->setJWTService(
                (new JWTService())
                    ->setRSA(new RSA())
                    ->setJWT(new JWT())
            );

        $this->initReflection($this->rolesMiddleware);
    }

    public function testSetAESService(): void
    {
        $this->assertInstanceOf(RolesMiddleware::class, $this->rolesMiddleware->setAESService(new AESService()));
        $this->assertInstanceOf(AESService::class, $this->getPrivateProperty('aESService'));
    }

    public function testSetJWTService(): void
    {
        $this->assertInstanceOf(RolesMiddleware::class, $this->rolesMiddleware->setJWTService(new JWTService()));
        $this->assertInstanceOf(JWTService::class, $this->getPrivateProperty('jWTService'));
    }

    /**
     * @throws MiddlewareException
     */
    public function testAccess(): void
    {
        $this
            ->exception(MiddlewareException::class)
            ->exceptionMessage('you do not have the necessary permissions to access this resource')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::FORBIDDEN)
            ->expectLionException(function (): void {
                $encode = $this->AESEncode([
                    'idroles' => (string) RolesEnum::CUSTOMER->value,
                ]);

                $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
                    'idroles' => $encode['idroles'],
                ]);

                $this->rolesMiddleware->access([RolesEnum::ADMINISTRATOR->value]);
            });
    }
}
