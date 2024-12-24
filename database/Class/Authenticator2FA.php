<?php

declare(strict_types=1);

namespace Database\Class;

use Lion\Bundle\Interface\CapsuleInterface;
use Lion\Bundle\Traits\CapsuleTrait;

/**
 * Capsule for the 'Authenticator2FA' entity
 *
 * @property string $entity [Entity name]
 * @property int $idusers [Property for idusers]
 * @property int $users_2fa [Property for users_2fa]
 * @property string $users_2fa_secret [Property for users_2fa_secret]
 * @property string $users_secret_code [Property for users_secret_code]
 *
 * @package Database\Class
 */
class Authenticator2FA implements CapsuleInterface
{
    use CapsuleTrait;

    /**
     * [Entity name]
     *
     * @var string $entity
     */
    private string $entity = '';

    /**
     * [Property for idusers]
     *
     * @var int|null $idusers
     */
    private ?int $idusers = null;

    /**
     * [Property for users_2fa]
     *
     * @var int|null $users_2fa
     */
    private ?int $users_2fa = null;

    /**
     * [Property for users_2fa_secret]
     *
     * @var string|null $users_2fa_secret
     */
    private ?string $users_2fa_secret = null;

    /**
     * [Property for users_secret_code]
     *
     * @var string|null $users_secret_code
     */
    private ?string $users_secret_code = null;

    /**
     * {@inheritdoc}
     * */
    public function capsule(): Authenticator2FA
    {
        $this
            ->setIdusers(request('idusers'))
            ->setUsers2fa(request('users_2fa'))
            ->setUsers2faSecret(request('users_2fa_secret'))
            ->setUsersSecretCode(request('users_secret_code'));

        return $this;
    }

    /**
     * Getter method for 'idusers'
     *
     * @return int|null
     */
    public function getIdusers(): ?int
    {
        return $this->idusers;
    }

    /**
     * Setter method for 'idusers'
     *
     * @param int|null $idusers
     *
     * @return Authenticator2FA
     */
    public function setIdusers(?int $idusers = null): Authenticator2FA
    {
        $this->idusers = $idusers;

        return $this;
    }

    /**
     * Getter method for 'users_2fa'
     *
     * @return int|null
     */
    public function getUsers2fa(): ?int
    {
        return $this->users_2fa;
    }

    /**
     * Setter method for 'users_2fa'
     *
     * @param int|null $users_2fa
     *
     * @return Authenticator2FA
     */
    public function setUsers2fa(?int $users_2fa = null): Authenticator2FA
    {
        $this->users_2fa = $users_2fa;

        return $this;
    }

    /**
     * Getter method for 'users_2fa_secret'
     *
     * @return string|null
     */
    public function getUsers2faSecret(): ?string
    {
        return $this->users_2fa_secret;
    }

    /**
     * Setter method for 'users_2fa_secret'
     *
     * @param string|null $users_2fa_secret
     *
     * @return Authenticator2FA
     */
    public function setUsers2faSecret(?string $users_2fa_secret = null): Authenticator2FA
    {
        $this->users_2fa_secret = $users_2fa_secret;

        return $this;
    }

    /**
     * Getter method for 'users_secret_code'
     *
     * @return string|null
     */
    public function getUsersSecretCode(): ?string
    {
        return $this->users_secret_code;
    }

    /**
     * Setter method for 'users_secret_code'
     *
     * @param string|null $users_secret_code
     *
     * @return Authenticator2FA
     */
    public function setUsersSecretCode(?string $users_secret_code = null): Authenticator2FA
    {
        $this->users_secret_code = $users_secret_code;

        return $this;
    }
}
