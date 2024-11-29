<?php

declare(strict_types=1);

namespace Tests\App\Http\Services;

use App\Http\Services\JWTService;
use Lion\Bundle\Test\Test;
use Lion\Security\JWT;
use Lion\Security\RSA;
use PHPUnit\Framework\Attributes\Test as Testing;
use ReflectionException;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;

class JWTServiceTest extends Test
{
    use AuthJwtProviderTrait;

    private JWTService $jWTService;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->jWTService = (new JWTService())
            ->setRSA(new RSA())
            ->setJWT(new JWT());

        $this->initReflection($this->jWTService);
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setRSA(): void
    {
        $this->assertInstanceOf(JWTService::class, $this->jWTService->setRSA(new RSA()));
        $this->assertInstanceOf(RSA::class, $this->getPrivateProperty('rsa'));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setJWT(): void
    {
        $this->assertInstanceOf(JWTService::class, $this->jWTService->setJWT(new JWT()));
        $this->assertInstanceOf(JWT::class, $this->getPrivateProperty('jwt'));
    }

    #[Testing]
    public function getTokenData(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization();

        $data = $this->jWTService->getTokenData(env('RSA_URL_PATH'));

        $this->assertIsObject($data);
        $this->assertInstanceOf(stdClass::class, $data);
        $this->assertObjectHasProperty('session', $data);
        $this->assertIsBool($data->session);
        $this->assertTrue($data->session);
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
    }

    #[Testing]
    public function getToken(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization();

        $data = $this->jWTService->getToken();

        $this->assertIsObject($data);
        $this->assertInstanceOf(stdClass::class, $data);
        $this->assertObjectHasProperty('data', $data);
        $this->assertObjectHasProperty('session', $data->data);
        $this->assertIsBool($data->data->session);
        $this->assertTrue($data->data->session);
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
    }

    #[Testing]
    public function decode(): void
    {
        $token = str->of($this->getAuthorization())->replace('Bearer', '')->trim()->get();

        $data = $this->jWTService->decode(env('RSA_URL_PATH'), $token);

        $this->assertIsObject($data);
        $this->assertInstanceOf(stdClass::class, $data);
        $this->assertObjectHasProperty('data', $data);
        $this->assertObjectHasProperty('session', $data->data);
        $this->assertIsBool($data->data->session);
        $this->assertTrue($data->data->session);
    }

    #[Testing]
    public function decodeWithAuthorization(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization();

        $data = $this->jWTService->decode(env('RSA_URL_PATH'));

        $this->assertIsObject($data);
        $this->assertInstanceOf(stdClass::class, $data);
        $this->assertObjectHasProperty('data', $data);
        $this->assertObjectHasProperty('session', $data->data);
        $this->assertIsBool($data->data->session);
        $this->assertTrue($data->data->session);
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
    }
}
