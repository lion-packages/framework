<?php

declare(strict_types=1);

namespace Tests\App\Http\Services;

use App\Http\Services\AESService;
use Lion\Security\AES;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;

class AESServiceTest extends Test
{
    private AESService $aESService;

    protected function setUp(): void
    {
        $this->aESService = (new AESService())
            ->setAES(new AES());

        $this->initReflection($this->aESService);
    }

    #[Testing]
    public function setAES(): void
    {
        $this->assertInstanceOf(AESService::class, $this->aESService->setAES(new AES()));
        $this->assertInstanceOf(AES::class, $this->getPrivateProperty('aes'));
    }

    #[Testing]
    public function encode(): void
    {
        $encode = $this->aESService->encode(['key' => 'example']);

        $this->assertIsArray($encode);
        $this->assertArrayHasKey('key', $encode);
        $this->assertIsString($encode['key']);
    }

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
