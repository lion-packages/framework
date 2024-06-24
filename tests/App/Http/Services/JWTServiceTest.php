<?php

declare(strict_types=1);

namespace Tests\App\Http\Services;

use App\Http\Services\JWTService;
use Lion\Security\JWT;
use Lion\Security\RSA;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Test;

class JWTServiceTest extends Test
{
    use AuthJwtProviderTrait;

    private JWTService $jWTService;

    protected function setUp(): void
    {
        $this->jWTService = (new JWTService())
            ->setRSA(new RSA())
            ->setJWT(new JWT());
    }

    public function testGetTokenData(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization();

        $data = $this->jWTService->getTokenData(env('RSA_URL_PATH'));

        $this->assertIsObject($data);
        $this->assertObjectHasProperty('session', $data);
        $this->assertTrue($data->session);
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
    }

    public function testGetToken(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization();

        $data = $this->jWTService->getToken();

        $this->assertIsObject($data);
        $this->assertObjectHasProperty('data', $data);
        $this->assertObjectHasProperty('session', $data->data);
        $this->assertTrue($data->data->session);
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
    }

    public function testDecode(): void
    {
        $token = str->of($this->getAuthorization())->replace('Bearer', '')->trim()->get();

        $data = $this->jWTService->decode(env('RSA_URL_PATH'), $token);

        $this->assertIsObject($data);
        $this->assertObjectHasProperty('data', $data);
        $this->assertObjectHasProperty('session', $data->data);
        $this->assertTrue($data->data->session);
    }

    public function testDecodeWithAuthorization(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization();

        $data = $this->jWTService->decode(env('RSA_URL_PATH'));

        $this->assertIsObject($data);
        $this->assertObjectHasProperty('data', $data);
        $this->assertObjectHasProperty('session', $data->data);
        $this->assertTrue($data->data->session);
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
    }
}
