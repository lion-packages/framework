<?php

declare(strict_types=1);

namespace Tests\Database\Class;

use Database\Class\PasswordManager;
use Lion\Bundle\Interface\CapsuleInterface;
use Tests\Test;

class PasswordManagerTest extends Test
{
    const int IDUSERS = 1;
    const string USERS_PASSWORD = 'cbfad02f9ed2a8d1e08d8f74f5303e9eb93637d47f82ab6f1c15871cf8dd0481';

    private PasswordManager $passwordManager;

    protected function setUp(): void
    {
        $this->passwordManager = new PasswordManager();
    }

    public function testCapsule(): void
    {
        $this->assertCapsule($this->passwordManager, '');
    }

    public function testGetIdusers(): void
    {
        $this->passwordManager->setIdusers(self::IDUSERS);

        $this->assertSame(self::IDUSERS, $this->passwordManager->getIdusers());
    }

    public function testSetIdusers(): void
    {
        $this->assertInstances($this->passwordManager->setIdusers(self::IDUSERS), [
            PasswordManager::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::IDUSERS, $this->passwordManager->getIdusers());
    }

    public function testGetUsersPassword(): void
    {
        $this->passwordManager->setUsersPassword(self::USERS_PASSWORD);

        $this->assertSame(self::USERS_PASSWORD, $this->passwordManager->getUsersPassword());
    }

    public function testSetUsersPassword(): void
    {
        $this->assertInstances($this->passwordManager->setUsersPassword(self::USERS_PASSWORD), [
            PasswordManager::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::USERS_PASSWORD, $this->passwordManager->getUsersPassword());
    }

    public function testGetUsersPasswordNew(): void
    {
        $this->passwordManager->setUsersPasswordNew(self::USERS_PASSWORD);

        $this->assertSame(self::USERS_PASSWORD, $this->passwordManager->getUsersPasswordNew());
    }

    public function testSetUsersPasswordNew(): void
    {
        $this->assertInstances($this->passwordManager->setUsersPasswordNew(self::USERS_PASSWORD), [
            PasswordManager::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::USERS_PASSWORD, $this->passwordManager->getUsersPasswordNew());
    }

    public function testGetUsersPasswordConfirm(): void
    {
        $this->passwordManager->setUsersPasswordConfirm(self::USERS_PASSWORD);

        $this->assertSame(self::USERS_PASSWORD, $this->passwordManager->getUsersPasswordConfirm());
    }

    public function testSetUsersPasswordConfirm(): void
    {
        $this->assertInstances($this->passwordManager->setUsersPasswordConfirm(self::USERS_PASSWORD), [
            PasswordManager::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::USERS_PASSWORD, $this->passwordManager->getUsersPasswordConfirm());
    }
}
