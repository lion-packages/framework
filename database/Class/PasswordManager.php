<?php

declare(strict_types=1);

namespace Database\Class;

use Lion\Bundle\Interface\CapsuleInterface;

/**
 * Capsule for the 'PasswordManager' entity
 *
 * @property int $idusers [Property for idusers]
 * @property string $users_password [Property for users_password]
 * @property string $users_password_new [Property for users_password_new]
 * @property string $users_password_confirm [Property for users_password_confirm]
 *
 * @package Database\Class
 */
class PasswordManager implements CapsuleInterface
{
	/**
	 * [Property for idusers]
	 *
	 * @var int|null $idusers
	 */
	private ?int $idusers = null;

	/**
	 * [Property for users_password]
	 *
	 * @var string|null $users_password
	 */
	private ?string $users_password = null;

	/**
	 * [Property for users_password_new]
	 *
	 * @var string|null $users_password_new
	 */
	private ?string $users_password_new = null;

	/**
	 * [Property for users_password_confirm]
	 *
	 * @var string|null $users_password_confirm
	 */
	private ?string $users_password_confirm = null;

	/**
	 * {@inheritdoc}
	 * */
	public function jsonSerialize(): array
	{
		return get_object_vars($this);
	}

	/**
	 * {@inheritdoc}
	 * */
	public function capsule(): PasswordManager
	{
		$this
			->setIdusers(request->idusers ?? null)
			->setUsersPassword(request->users_password ?? null)
			->setUsersPasswordNew(request->users_password_new ?? null)
			->setUsersPasswordConfirm(request->users_password_confirm ?? null);

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
     * @return PasswordManager
     */
    public function setIdusers(?int $idusers = null): PasswordManager
    {
        $this->idusers = $idusers;

        return $this;
    }

    /**
     * Getter method for 'users_password'
     *
     * @return string|null
     */
    public function getUsersPassword(): ?string
    {
        return $this->users_password;
    }

    /**
     * Setter method for 'users_password'
     *
     * @param string|null $users_password
     *
     * @return PasswordManager
     */
    public function setUsersPassword(?string $users_password = null): PasswordManager
    {
        $this->users_password = $users_password;

        return $this;
    }

    /**
     * Getter method for 'users_password_new'
     *
     * @return string|null
     */
    public function getUsersPasswordNew(): ?string
    {
        return $this->users_password_new;
    }

    /**
     * Setter method for 'users_password_new'
     *
     * @param string|null $users_password_new
     *
     * @return PasswordManager
     */
    public function setUsersPasswordNew(?string $users_password_new = null): PasswordManager
    {
        $this->users_password_new = $users_password_new;

        return $this;
    }

    /**
     * Getter method for 'users_password_confirm'
     *
     * @return string|null
     */
    public function getUsersPasswordConfirm(): ?string
    {
        return $this->users_password_confirm;
    }

    /**
     * Setter method for 'users_password_confirm'
     *
     * @param string|null $users_password_confirm
     *
     * @return PasswordManager
     */
    public function setUsersPasswordConfirm(?string $users_password_confirm = null): PasswordManager
    {
        $this->users_password_confirm = $users_password_confirm;

        return $this;
    }
}
