<?php

declare(strict_types=1);

namespace Tests\App\Http\Services;

use App\Http\Services\AESService;
use Lion\Security\AES;
use Lion\Test\Test;

class AESServiceTest extends Test
{
    private AESService $aESService;

    protected function setUp(): void
    {
        $this->aESService = (new AESService())
            ->setAES(new AES());

        $this->initReflection($this->aESService);
    }

    public function testSetAES(): void
    {
        $this->assertInstanceOf(AESService::class, $this->aESService->setAES(new AES()));
        $this->assertInstanceOf(AES::class, $this->getPrivateProperty('aes'));
    }

    public function testEncode(): void
    {
        $encode = $this->aESService->encode(['key' => 'example']);

        $this->assertIsArray($encode);
        $this->assertArrayHasKey('key', $encode);
        $this->assertIsString($encode['key']);
    }

    public function testDecode(): void
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
