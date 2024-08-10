<?php

declare(strict_types=1);

namespace Tests\Database\Class;

use Database\Class\Authenticator2FA;
use Lion\Bundle\Interface\CapsuleInterface;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Test;

class Authenticator2FATest extends Test
{
    private const int IDUSERS = 1;
    private const int USERS_2FA = 0;
    private const string USERS_2FA_SECRET = '################';
    private const string USERS_SECRET_CODE = '000000';

    private Authenticator2FA $authenticator2FA;

    protected function setUp(): void
    {
        $this->authenticator2FA = new Authenticator2FA();
    }

    public function testCapsule(): void
    {
        $this->assertCapsule($this->authenticator2FA, '');
    }

    #[Testing]
    public function getIdusers(): void
    {
        $this->authenticator2FA->setIdusers(self::IDUSERS);

        $this->assertSame(self::IDUSERS, $this->authenticator2FA->getIdusers());
    }

    #[Testing]
    public function setIdusers(): void
    {
        $this->assertInstances($this->authenticator2FA->setIdusers(self::IDUSERS), [
            Authenticator2FA::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::IDUSERS, $this->authenticator2FA->getIdusers());
    }

    #[Testing]
    public function getUsers2fa(): void
    {
        $this->authenticator2FA->setUsers2fa(self::USERS_2FA);

        $this->assertSame(self::USERS_2FA, $this->authenticator2FA->getUsers2fa());
    }

    #[Testing]
    public function setUsers2fa(): void
    {
        $this->assertInstances($this->authenticator2FA->setUsers2fa(self::USERS_2FA), [
            Authenticator2FA::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::USERS_2FA, $this->authenticator2FA->getUsers2fa());
    }

    #[Testing]
    public function getUsers2faSecret(): void
    {
        $this->authenticator2FA->setUsers2faSecret(self::USERS_2FA_SECRET);

        $this->assertSame(self::USERS_2FA_SECRET, $this->authenticator2FA->getUsers2faSecret());
    }

    #[Testing]
    public function setUsers2faSecret(): void
    {
        $this->assertInstances($this->authenticator2FA->setUsers2faSecret(self::USERS_2FA_SECRET), [
            Authenticator2FA::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::USERS_2FA_SECRET, $this->authenticator2FA->getUsers2faSecret());
    }

    #[Testing]
    public function getUsersSecretCode(): void
    {
        $this->authenticator2FA->setUsersSecretCode(self::USERS_SECRET_CODE);

        $this->assertSame(self::USERS_SECRET_CODE, $this->authenticator2FA->getUsersSecretCode());
    }

    #[Testing]
    public function setUsersSecretCode(): void
    {
        $this->assertInstances($this->authenticator2FA->setUsersSecretCode(self::USERS_SECRET_CODE), [
            Authenticator2FA::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::USERS_SECRET_CODE, $this->authenticator2FA->getUsersSecretCode());
    }
}
