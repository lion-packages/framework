<?php

declare(strict_types=1);

namespace Tests\Database\Class\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use App\Enums\RolesEnum;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Bundle\Interface\CapsuleInterface;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Test;

class UsersTest extends Test
{
    private const string ENTITY = 'users';
    private const int IDUSERS = 1;
    private const string USERS_CITIZEN_IDENTIFICATION = '1234567890';
    private const string USERS_NAME = 'Sergio';
    private const string USERS_LAST_NAME = 'Leon';
    private const string USERS_NICKNAME = 'Sleon';
    private const string USERS_ACTIVATION_CODE = '######';
    private const string USERS_RECOVERY_CODE = '######';
    private const string USERS_CODE = 'code-65ca2d74ed1e1';

    private Users $users;

    protected function setUp(): void
    {
        $this->users = new Users();
    }

    #[Testing]
    public function capsule(): void
    {
        $this->assertCapsule($this->users, self::ENTITY);
    }

    #[Testing]
    public function getIdusers(): void
    {
        $this->users->setIdusers(self::IDUSERS);

        $this->assertIsInt($this->users->getIdusers());
        $this->assertSame(self::IDUSERS, $this->users->getIdusers());
    }

    #[Testing]
    public function setIdusers(): void
    {
        $this->assertInstances($this->users->setIdusers(self::IDUSERS), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsInt($this->users->getIdusers());
        $this->assertSame(self::IDUSERS, $this->users->getIdusers());
    }

    #[Testing]
    public function getIdroles(): void
    {
        $this->users->setIdroles(RolesEnum::ADMINISTRATOR->value);

        $this->assertIsInt($this->users->getIdroles());
        $this->assertSame(RolesEnum::ADMINISTRATOR->value, $this->users->getIdroles());
    }

    #[Testing]
    public function setIdroles(): void
    {
        $this->assertInstances($this->users->setIdroles(RolesEnum::ADMINISTRATOR->value), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsInt($this->users->getIdroles());
        $this->assertSame(RolesEnum::ADMINISTRATOR->value, $this->users->getIdroles());
    }

    #[Testing]
    public function getIddocumentTypes(): void
    {
        $this->users->setIddocumentTypes(DocumentTypesEnum::CITIZENSHIP_CARD->value);

        $this->assertIsInt($this->users->getIddocumentTypes());
        $this->assertSame(DocumentTypesEnum::CITIZENSHIP_CARD->value, $this->users->getIddocumentTypes());
    }

    #[Testing]
    public function setIddocumentTypes(): void
    {
        $this->assertInstances($this->users->setIddocumentTypes(DocumentTypesEnum::CITIZENSHIP_CARD->value), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsInt($this->users->getIddocumentTypes());
        $this->assertSame(DocumentTypesEnum::CITIZENSHIP_CARD->value, $this->users->getIddocumentTypes());
    }

    #[Testing]
    public function getUsersCitizenIdentification(): void
    {
        $this->users->setUsersCitizenIdentification(self::USERS_CITIZEN_IDENTIFICATION);

        $this->assertIsString($this->users->getUsersCitizenIdentification());
        $this->assertSame(self::USERS_CITIZEN_IDENTIFICATION, $this->users->getUsersCitizenIdentification());
    }

    #[Testing]
    public function setUsersCitizenIdentification(): void
    {
        $this->assertInstances($this->users->setUsersCitizenIdentification(self::USERS_CITIZEN_IDENTIFICATION), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsString($this->users->getUsersCitizenIdentification());
        $this->assertSame(self::USERS_CITIZEN_IDENTIFICATION, $this->users->getUsersCitizenIdentification());
    }

    #[Testing]
    public function getUsersName(): void
    {
        $this->users->setUsersName(self::USERS_NAME);

        $this->assertIsString($this->users->getUsersName());
        $this->assertSame(self::USERS_NAME, $this->users->getUsersName());
    }

    #[Testing]
    public function setUsersName(): void
    {
        $this->assertInstances($this->users->setUsersName(self::USERS_NAME), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsString($this->users->getUsersName());
        $this->assertSame(self::USERS_NAME, $this->users->getUsersName());
    }

    #[Testing]
    public function getUsersLastName(): void
    {
        $this->users->setUsersLastName(self::USERS_LAST_NAME);

        $this->assertIsString($this->users->getUsersLastName());
        $this->assertSame(self::USERS_LAST_NAME, $this->users->getUsersLastName());
    }

    #[Testing]
    public function setUsersLastName(): void
    {
        $this->assertInstances($this->users->setUsersLastName(self::USERS_LAST_NAME), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsString($this->users->getUsersLastName());
        $this->assertSame(self::USERS_LAST_NAME, $this->users->getUsersLastName());
    }

    #[Testing]
    public function getUsersNickname(): void
    {
        $this->users->setUsersNickname(self::USERS_NICKNAME);

        $this->assertIsString($this->users->getUsersNickname());
        $this->assertSame(self::USERS_NICKNAME, $this->users->getUsersNickname());
    }

    #[Testing]
    public function setUsersNickname(): void
    {
        $this->assertInstances($this->users->setUsersNickname(self::USERS_NICKNAME), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsString($this->users->getUsersNickname());
        $this->assertSame(self::USERS_NICKNAME, $this->users->getUsersNickname());
    }

    #[Testing]
    public function getUsersEmail(): void
    {
        $this->users->setUsersEmail(UsersFactory::USERS_EMAIL);

        $this->assertIsString($this->users->getUsersEmail());
        $this->assertSame(UsersFactory::USERS_EMAIL, $this->users->getUsersEmail());
    }

    #[Testing]
    public function setUsersEmail(): void
    {
        $this->assertInstances($this->users->setUsersEmail(UsersFactory::USERS_EMAIL), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsString($this->users->getUsersEmail());
        $this->assertSame(UsersFactory::USERS_EMAIL, $this->users->getUsersEmail());
    }

    #[Testing]
    public function getUsersPassword(): void
    {
        $this->users->setUsersPassword(UsersFactory::USERS_PASSWORD_HASH);

        $this->assertIsString($this->users->getUsersPassword());
        $this->assertSame(UsersFactory::USERS_PASSWORD_HASH, $this->users->getUsersPassword());
    }

    #[Testing]
    public function setUsersPassword(): void
    {
        $this->assertInstances($this->users->setUsersPassword(UsersFactory::USERS_PASSWORD_HASH), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsString($this->users->getUsersPassword());
        $this->assertSame(UsersFactory::USERS_PASSWORD_HASH, $this->users->getUsersPassword());
    }

    #[Testing]
    public function getUsersActivationCode(): void
    {
        $this->users->setUsersActivationCode(self::USERS_ACTIVATION_CODE);

        $this->assertIsString($this->users->getUsersActivationCode());
        $this->assertSame(self::USERS_ACTIVATION_CODE, $this->users->getUsersActivationCode());
    }

    #[Testing]
    public function setUsersActivationCode(): void
    {
        $this->assertInstances($this->users->setUsersActivationCode(self::USERS_ACTIVATION_CODE), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsString($this->users->getUsersActivationCode());
        $this->assertSame(self::USERS_ACTIVATION_CODE, $this->users->getUsersActivationCode());
    }

    #[Testing]
    public function getUsersRecoveryCode(): void
    {
        $this->users->setUsersRecoveryCode(self::USERS_RECOVERY_CODE);

        $this->assertIsString($this->users->getUsersRecoveryCode());
        $this->assertSame(self::USERS_RECOVERY_CODE, $this->users->getUsersRecoveryCode());
    }

    #[Testing]
    public function setUsersRecoveryCode(): void
    {
        $this->assertInstances($this->users->setUsersRecoveryCode(self::USERS_RECOVERY_CODE), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsString($this->users->getUsersRecoveryCode());
        $this->assertSame(self::USERS_RECOVERY_CODE, $this->users->getUsersRecoveryCode());
    }

    #[Testing]
    public function getUsersCode(): void
    {
        $this->users->setUsersCode(self::USERS_CODE);

        $this->assertIsString($this->users->getUsersCode());
        $this->assertSame(self::USERS_CODE, $this->users->getUsersCode());
    }

    #[Testing]
    public function setUsersCode(): void
    {
        $this->assertInstances($this->users->setUsersCode(self::USERS_CODE), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsString($this->users->getUsersCode());
        $this->assertSame(self::USERS_CODE, $this->users->getUsersCode());
    }

    #[Testing]
    public function getUsers2fa(): void
    {
        $this->users->setUsers2fa(UsersFactory::ENABLED_2FA);

        $this->assertIsInt($this->users->getUsers2fa());
        $this->assertSame(UsersFactory::ENABLED_2FA, $this->users->getUsers2fa());
    }

    #[Testing]
    public function setUsers2fa(): void
    {
        $this->assertInstances($this->users->setUsers2fa(UsersFactory::ENABLED_2FA), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsInt($this->users->getUsers2fa());
        $this->assertSame(UsersFactory::ENABLED_2FA, $this->users->getUsers2fa());
    }

    #[Testing]
    public function getUsers2faSecret(): void
    {
        $this->users->setUsers2faSecret(UsersFactory::SECURITY_KEY_2FA);

        $this->assertIsString($this->users->getUsers2faSecret());
        $this->assertSame(UsersFactory::SECURITY_KEY_2FA, $this->users->getUsers2faSecret());
    }

    #[Testing]
    public function setUsers2faSecret(): void
    {
        $this->assertInstances($this->users->setUsers2faSecret(UsersFactory::SECURITY_KEY_2FA), [
            Users::class,
            CapsuleInterface::class,
        ]);

        $this->assertIsString($this->users->getUsers2faSecret());
        $this->assertSame(UsersFactory::SECURITY_KEY_2FA, $this->users->getUsers2faSecret());
    }
}
