<?php

declare(strict_types=1);

namespace Tests\Database\Class\LionDatabase\MySQL;

use Database\Class\LionDatabase\MySQL\Roles;
use Lion\Bundle\Interface\CapsuleInterface;
use Tests\Test;

class RolesTest extends Test
{
    private const string ENTITY = 'roles';
    private const int IDROLES = 1;
    private const string ROLES_NAME = 'Administrator';
    private const string ROLES_DESCRIPTION = 'Administrator description';

    private Roles $roles;

    protected function setUp(): void
    {
        $this->roles = new Roles();
    }

    public function testCapsule(): void
    {
        $this->assertCapsule($this->roles, self::ENTITY);
    }

    public function testGetIdroles(): void
    {
        $this->roles->setIdroles(self::IDROLES);

        $this->assertSame(self::IDROLES, $this->roles->getIdroles());
    }

    public function testSetIdroles(): void
    {
        $this->assertInstances($this->roles->setIdroles(self::IDROLES), [
            Roles::class,
            CapsuleInterface::class
        ]);

        $this->assertSame(self::IDROLES, $this->roles->getIdroles());
    }

    public function testGetRolesName(): void
    {
        $this->roles->setRolesName(self::ROLES_NAME);

        $this->assertSame(self::ROLES_NAME, $this->roles->getRolesName());
    }

    public function testSetRolesName(): void
    {
        $this->assertInstances($this->roles->setRolesName(self::ROLES_NAME), [
            Roles::class,
            CapsuleInterface::class
        ]);

        $this->assertSame(self::ROLES_NAME, $this->roles->getRolesName());
    }

    public function testGetRolesDescription(): void
    {
        $this->roles->setRolesDescription(self::ROLES_DESCRIPTION);

        $this->assertSame(self::ROLES_DESCRIPTION, $this->roles->getRolesDescription());
    }

    public function testSetRolesDescription(): void
    {
        $this->assertInstances($this->roles->setRolesDescription(self::ROLES_DESCRIPTION), [
            Roles::class,
            CapsuleInterface::class
        ]);

        $this->assertSame(self::ROLES_DESCRIPTION, $this->roles->getRolesDescription());
    }
}
