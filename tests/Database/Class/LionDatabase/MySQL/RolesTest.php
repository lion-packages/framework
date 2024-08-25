<?php

declare(strict_types=1);

namespace Tests\Database\Class\LionDatabase\MySQL;

use Database\Class\LionDatabase\MySQL\Roles;
use Lion\Bundle\Interface\CapsuleInterface;
use PHPUnit\Framework\Attributes\Test as Testing;
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

    #[Testing]
    public function capsule(): void
    {
        $this->assertCapsule($this->roles, self::ENTITY);
    }

    #[Testing]
    public function getIdroles(): void
    {
        $this->roles->setIdroles(self::IDROLES);

        $this->assertIsInt($this->roles->getIdroles());
        $this->assertSame(self::IDROLES, $this->roles->getIdroles());
    }

    #[Testing]
    public function setIdroles(): void
    {
        $this->assertInstances($this->roles->setIdroles(self::IDROLES), [
            Roles::class,
            CapsuleInterface::class
        ]);

        $this->assertIsInt($this->roles->getIdroles());
        $this->assertSame(self::IDROLES, $this->roles->getIdroles());
    }

    #[Testing]
    public function getRolesName(): void
    {
        $this->roles->setRolesName(self::ROLES_NAME);

        $this->assertIsString($this->roles->getRolesName());
        $this->assertSame(self::ROLES_NAME, $this->roles->getRolesName());
    }

    #[Testing]
    public function setRolesName(): void
    {
        $this->assertInstances($this->roles->setRolesName(self::ROLES_NAME), [
            Roles::class,
            CapsuleInterface::class
        ]);

        $this->assertIsString($this->roles->getRolesName());
        $this->assertSame(self::ROLES_NAME, $this->roles->getRolesName());
    }

    #[Testing]
    public function getRolesDescription(): void
    {
        $this->roles->setRolesDescription(self::ROLES_DESCRIPTION);

        $this->assertIsString($this->roles->getRolesDescription());
        $this->assertSame(self::ROLES_DESCRIPTION, $this->roles->getRolesDescription());
    }

    #[Testing]
    public function setRolesDescription(): void
    {
        $this->assertInstances($this->roles->setRolesDescription(self::ROLES_DESCRIPTION), [
            Roles::class,
            CapsuleInterface::class
        ]);

        $this->assertIsString($this->roles->getRolesDescription());
        $this->assertSame(self::ROLES_DESCRIPTION, $this->roles->getRolesDescription());
    }
}
