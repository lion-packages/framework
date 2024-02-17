<?php

declare(strict_types=1);

namespace Tests\Class\LionDatabase\MySQL;

use Database\Class\LionDatabase\MySQL\Users;
use JsonSerializable;
use Lion\Bundle\Interface\CapsuleInterface;
use Lion\Test\Test;

class UsersTest extends Test
{
    const IDUSERS = 1;
    const IDROLES = 1;
    const IDDOCUMENT_TYPES = 1;
    const USERS_NAME = 'Sergio';
    const USERS_LAST_NAME = 'Leon';
    const USERS_EMAIL = 'sleon@dev.com';
    const USERS_PASSWORD = 'cbfad02f9ed2a8d1e08d8f74f5303e9eb93637d47f82ab6f1c15871cf8dd0481';
    const USERS_CODE = 'code-65ca2d74ed1e1';

    private Users $users;

	protected function setUp(): void
	{
        $this->users = new Users();
	}

    public function testGetIdusers(): void
    {
        $this->assertSame(self::IDUSERS, $this->users->setIdusers(self::IDUSERS)->getIdusers());
    }

    public function testSetIdusers(): void
    {
        $this->assertInstances($this->users->setIdusers(self::IDUSERS), [
            Users::class,
            CapsuleInterface::class,
            JsonSerializable::class
        ]);

        $this->assertSame(self::IDUSERS, $this->users->getIdusers());
    }

    public function testGetIdroles(): void
    {
        $this->assertSame(self::IDROLES, $this->users->setIdroles(self::IDROLES)->getIdroles());
    }

    public function testSetIdroles(): void
    {
        $this->assertInstances($this->users->setIdroles(self::IDROLES), [
            Users::class,
            CapsuleInterface::class,
            JsonSerializable::class
        ]);

        $this->assertSame(self::IDROLES, $this->users->getIdroles());
    }

    public function testGetIddocumentTypes(): void
    {
        $this->assertSame(
            self::IDDOCUMENT_TYPES,
            $this->users->setIddocumentTypes(self::IDDOCUMENT_TYPES)->getIddocumentTypes()
        );
    }

    public function testSetIddocumentTypes(): void
    {
        $this->assertInstances($this->users->setIddocumentTypes(self::IDDOCUMENT_TYPES), [
            Users::class,
            CapsuleInterface::class,
            JsonSerializable::class
        ]);

        $this->assertSame(self::IDDOCUMENT_TYPES, $this->users->getIddocumentTypes());
    }

    public function testGetUsersName(): void
    {
        $this->assertSame(self::USERS_NAME, $this->users->setUsersName(self::USERS_NAME)->getUsersName());
    }

    public function testSetUsersName(): void
    {
        $this->assertInstances($this->users->setUsersName(self::USERS_NAME), [
            Users::class,
            CapsuleInterface::class,
            JsonSerializable::class
        ]);

        $this->assertSame(self::USERS_NAME, $this->users->getUsersName());
    }

    public function testGetUsersLastName(): void
    {
        $this->assertSame(
            self::USERS_LAST_NAME,
            $this->users->setUsersLastName(self::USERS_LAST_NAME)->getUsersLastName()
        );
    }

    public function testSetUsersLastName(): void
    {
        $this->assertInstances($this->users->setUsersLastName(self::USERS_LAST_NAME), [
            Users::class,
            CapsuleInterface::class,
            JsonSerializable::class
        ]);

        $this->assertSame(self::USERS_LAST_NAME, $this->users->getUsersLastName());
    }

    public function testGetUsersEmail(): void
    {
        $this->assertSame(self::USERS_EMAIL, $this->users->setUsersEmail(self::USERS_EMAIL)->getUsersEmail());
    }

    public function testSetUsersEmail(): void
    {
        $this->assertInstances($this->users->setUsersEmail(self::USERS_EMAIL), [
            Users::class,
            CapsuleInterface::class,
            JsonSerializable::class
        ]);

        $this->assertSame(self::USERS_EMAIL, $this->users->getUsersEmail());
    }

    public function testGetUsersPassword(): void
    {
        $this->assertSame(
            self::USERS_PASSWORD,
            $this->users->setUsersPassword(self::USERS_PASSWORD)->getUsersPassword()
        );
    }

    public function testSetUsersPassword(): void
    {
        $this->assertInstances($this->users->setUsersPassword(self::USERS_PASSWORD), [
            Users::class,
            CapsuleInterface::class,
            JsonSerializable::class
        ]);

        $this->assertSame(self::USERS_PASSWORD, $this->users->getUsersPassword());
    }

    public function testGetUsersCode(): void
    {
        $this->assertSame(self::USERS_CODE, $this->users->setUsersCode(self::USERS_CODE)->getUsersCode());
    }

    public function testSetUsersCode(): void
    {
        $this->assertInstances($this->users->setUsersCode(self::USERS_CODE), [
            Users::class,
            CapsuleInterface::class,
            JsonSerializable::class
        ]);

        $this->assertSame(self::USERS_CODE, $this->users->getUsersCode());
    }
}
