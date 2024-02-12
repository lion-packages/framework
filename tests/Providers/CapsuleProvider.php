<?php

declare(strict_types=1);

namespace Tests\Providers;

use JsonSerializable;
use Lion\Bundle\Interface\CapsuleInterface;
use Lion\Test\Test;

abstract class CapsuleProvider extends Test
{
    public function assertJsonSerialize(JsonSerializable $jsonSerializable, array $propierties): void
    {
        $serialize = $jsonSerializable->jsonSerialize();

        $this->assertIsArray($serialize);
        $this->assertSame($propierties, $serialize);
    }

    public function assertCapsule(CapsuleInterface $capsuleInterface, string $class): void
    {
        $this->assertInstanceOf($class, $capsuleInterface);
        $this->assertIsObject($capsuleInterface->capsule());
    }
}
