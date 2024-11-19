<?php

declare(strict_types=1);

namespace Tests;

use Lion\Bundle\Interface\CapsuleInterface;
use Lion\Test\Test as TestTest;

/**
 * The TestCase extended abstract test class allows you to write unit tests in
 * PHP using the PHPUnit framework
 *
 * @package Tests
 */
class Test extends TestTest
{
    /**
     * Removes properties from an array and asserts whether they were completely
     * removed
     *
     * @param array $list [Item List]
     * @param array $properties [List of properties to remove]
     *
     * @return void
     */
    final public function assertArrayNotHasKeyFromList(array $list, array $properties): void
    {
        foreach ($properties as $property) {
            if (isset($list[$property])) {
                unset($list[$property]);

                $this->assertArrayNotHasKey($property, $list);
            }
        }
    }

    /**
     * Checks two aspects of an object that implements the CapsuleInterface
     * interface
     *
     * @param CapsuleInterface $capsuleInterface [Implement abstract methods for
     * capsule classes]
     * @param string $entity [Entity name]
     *
     * @return void
     */
    public function assertCapsule(CapsuleInterface $capsuleInterface, string $entity): void
    {
        $this->assertInstanceOf(CapsuleInterface::class, $capsuleInterface->capsule());
        $this->assertIsArray($capsuleInterface->jsonSerialize());
        $this->assertIsString($capsuleInterface->getTableName());
        $this->assertSame($entity, $capsuleInterface->getTableName());
    }
}
