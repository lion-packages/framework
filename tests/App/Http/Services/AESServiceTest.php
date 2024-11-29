<?php

declare(strict_types=1);

namespace Tests\App\Http\Services;

use App\Http\Services\AESService;
use Lion\Bundle\Test\Test;
use Lion\Security\AES;
use Lion\Security\Exceptions\AESException;
use PHPUnit\Framework\Attributes\Test as Testing;
use ReflectionException;

class AESServiceTest extends Test
{
    private AESService $aESService;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->aESService = (new AESService())
            ->setAES(new AES());

        $this->initReflection($this->aESService);
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setAES(): void
    {
        $this->assertInstanceOf(AESService::class, $this->aESService->setAES(new AES()));
        $this->assertInstanceOf(AES::class, $this->getPrivateProperty('aes'));
    }

    /**
     * @throws AESException
     */
    #[Testing]
    public function encode(): void
    {
        $encode = $this->aESService->encode(['key' => 'example']);

        $this->assertIsArray($encode);
        $this->assertArrayHasKey('key', $encode);
        $this->assertIsString($encode['key']);
    }

    /**
     * @throws AESException
     */
    #[Testing]
    public function decode(): void
    {
        $encode = $this->aESService->encode(['key' => 'example']);

        $this->assertIsArray($encode);
        $this->assertArrayHasKey('key', $encode);
        $this->assertIsString($encode['key']);

        $decode = $this->aESService->decode($encode);

        $this->assertIsArray($decode);
        $this->assertArrayHasKey('key', $decode);
        $this->assertIsString($decode['key']);
        $this->assertSame('example', $decode['key']);
    }
}
