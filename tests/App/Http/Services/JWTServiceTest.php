<?php

declare(strict_types=1);

namespace Tests\App\Http\Services;

use App\Http\Services\JWTService;
use Lion\Dependency\Injection\Container;
use Lion\Test\Test;
use Tests\Providers\AuthJwtProviderTrait;

class JWTServiceTest extends Test
{
    use AuthJwtProviderTrait;

    private JWTService $jWTService;

    protected function setUp(): void
    {
        $this->jWTService = (new Container())
            ->injectDependencies(new JWTService());
    }

    public function testGetTokenData(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization();

        $data = $this->jWTService->getTokenData(storage_path(env('RSA_URL_PATH'), false));

        $this->assertIsObject($data);
        $this->assertObjectHasProperty('session', $data);
        $this->assertTrue($data->session);
    }
}
