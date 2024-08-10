<?php

declare(strict_types=1);

namespace Tests\Database\Class;

use Database\Class\PasswordManager;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Bundle\Interface\CapsuleInterface;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Test;

class PasswordManagerTest extends Test
{
    private const int IDUSERS = 1;

    private PasswordManager $passwordManager;

    protected function setUp(): void
    {
        $this->passwordManager = new PasswordManager();
    }

    public function testCapsule(): void
    {
        $this->assertCapsule($this->passwordManager, '');
    }

    #[Testing]
    public function getIdusers(): void
    {
        $this->passwordManager->setIdusers(self::IDUSERS);

        $this->assertSame(self::IDUSERS, $this->passwordManager->getIdusers());
    }

    #[Testing]
    public function setIdusers(): void
    {
        $this->assertInstances($this->passwordManager->setIdusers(self::IDUSERS), [
            PasswordManager::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::IDUSERS, $this->passwordManager->getIdusers());
    }

    #[Testing]
    public function getUsersPassword(): void
    {
        $this->passwordManager->setUsersPassword(UsersFactory::USERS_PASSWORD_HASH);

        $this->assertSame(UsersFactory::USERS_PASSWORD_HASH, $this->passwordManager->getUsersPassword());
    }

    #[Testing]
    public function setUsersPassword(): void
    {
        $this->assertInstances($this->passwordManager->setUsersPassword(UsersFactory::USERS_PASSWORD_HASH), [
            PasswordManager::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(UsersFactory::USERS_PASSWORD_HASH, $this->passwordManager->getUsersPassword());
    }

    #[Testing]
    public function getUsersPasswordNew(): void
    {
        $this->passwordManager->setUsersPasswordNew(UsersFactory::USERS_PASSWORD_HASH);

        $this->assertSame(UsersFactory::USERS_PASSWORD_HASH, $this->passwordManager->getUsersPasswordNew());
    }

    #[Testing]
    public function setUsersPasswordNew(): void
    {
        $this->assertInstances($this->passwordManager->setUsersPasswordNew(UsersFactory::USERS_PASSWORD_HASH), [
            PasswordManager::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(UsersFactory::USERS_PASSWORD_HASH, $this->passwordManager->getUsersPasswordNew());
    }

    #[Testing]
    public function getUsersPasswordConfirm(): void
    {
        $this->passwordManager->setUsersPasswordConfirm(UsersFactory::USERS_PASSWORD_HASH);

        $this->assertSame(UsersFactory::USERS_PASSWORD_HASH, $this->passwordManager->getUsersPasswordConfirm());
    }

    #[Testing]
    public function setUsersPasswordConfirm(): void
    {
        $this->assertInstances($this->passwordManager->setUsersPasswordConfirm(UsersFactory::USERS_PASSWORD_HASH), [
            PasswordManager::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(UsersFactory::USERS_PASSWORD_HASH, $this->passwordManager->getUsersPasswordConfirm());
    }
}
