<?php

declare(strict_types=1);

namespace Database\Class\LionDatabase\MySQL;

use Lion\Bundle\Interface\CapsuleInterface;

/**
 * Capsule for the 'Users' entity
 *
 * @property int $idusers [Property for idusers]
 * @property int $idroles [Property for idroles]
 * @property int $iddocument_types [Property for iddocument_types]
 * @property string $users_citizen_identification [Property for users_citizen_identification]
 * @property string $users_name [Property for users_name]
 * @property string $users_last_name [Property for users_last_name]
 * @property string $users_nickname [Property for users_nickname]
 * @property string $users_email [Property for users_email]
 * @property string $users_password [Property for users_password]
 * @property string $users_activation_code [Property for users_activation_code]
 * @property string $users_recovery_code [Property for users_recovery_code]
 * @property string $users_code [Property for users_code]
 * @property int $users_2fa [Property for users_2fa]
 * @property string $users_2fa_secret [Property for users_2fa_secret]
 *
 * @package Database\Class\LionDatabase\MySQL
 */
class Users implements CapsuleInterface
{
	/**
	 * [Property for idusers]
	 *
	 * @var int|null $idusers
	 */
	private ?int $idusers = null;

	/**
	 * [Property for idroles]
	 *
	 * @var int|null $idroles
	 */
	private ?int $idroles = null;

	/**
	 * [Property for iddocument_types]
	 *
	 * @var int|null $iddocument_types
	 */
	private ?int $iddocument_types = null;

	/**
	 * [Property for users_citizen_identification]
	 *
	 * @var string|null $users_citizen_identification
	 */
	private ?string $users_citizen_identification = null;

	/**
	 * [Property for users_name]
	 *
	 * @var string|null $users_name
	 */
	private ?string $users_name = null;

	/**
	 * [Property for users_last_name]
	 *
	 * @var string|null $users_last_name
	 */
	private ?string $users_last_name = null;

	/**
	 * [Property for users_nickname]
	 *
	 * @var string|null $users_nickname
	 */
	private ?string $users_nickname = null;

	/**
	 * [Property for users_email]
	 *
	 * @var string|null $users_email
	 */
	private ?string $users_email = null;

	/**
	 * [Property for users_password]
	 *
	 * @var string|null $users_password
	 */
	private ?string $users_password = null;

	/**
	 * [Property for users_activation_code]
	 *
	 * @var string|null $users_activation_code
	 */
	private ?string $users_activation_code = null;

	/**
	 * [Property for users_recovery_code]
	 *
	 * @var string|null $users_recovery_code
	 */
	private ?string $users_recovery_code = null;

	/**
	 * [Property for users_code]
	 *
	 * @var string|null $users_code
	 */
	private ?string $users_code = null;

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
	 * {@inheritdoc}
	 * */
	public function jsonSerialize(): array
	{
		return get_object_vars($this);
	}

	/**
	 * {@inheritdoc}
	 * */
	public function capsule(): Users
	{
		$this
			->setIdusers(request('idusers'))
			->setIdroles(request('idroles'))
			->setIddocumentTypes(request('iddocument_types'))
			->setUsersCitizenIdentification(request('users_citizen_identification'))
			->setUsersName(request('users_name'))
			->setUsersLastName(request('users_last_name'))
			->setUsersNickname(request('users_nickname'))
			->setUsersEmail(request('users_email'))
			->setUsersPassword(request('users_password'))
			->setUsersActivationCode(request('users_activation_code'))
			->setUsersRecoveryCode(request('users_recovery_code'))
			->setUsersCode(request('users_code'))
			->setUsers2fa(request('users_2fa'))
			->setUsers2faSecret(request('users_2fa_secret'));

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
     * @return Users
     */
    public function setIdusers(?int $idusers = null): Users
    {
        $this->idusers = $idusers;

        return $this;
    }

    /**
     * Getter method for 'idroles'
     *
     * @return int|null
     */
    public function getIdroles(): ?int
    {
        return $this->idroles;
    }

    /**
     * Setter method for 'idroles'
     *
     * @param int|null $idroles
     *
     * @return Users
     */
    public function setIdroles(?int $idroles = null): Users
    {
        $this->idroles = $idroles;

        return $this;
    }

    /**
     * Getter method for 'iddocument_types'
     *
     * @return int|null
     */
    public function getIddocumentTypes(): ?int
    {
        return $this->iddocument_types;
    }

    /**
     * Setter method for 'iddocument_types'
     *
     * @param int|null $iddocument_types
     *
     * @return Users
     */
    public function setIddocumentTypes(?int $iddocument_types = null): Users
    {
        $this->iddocument_types = $iddocument_types;

        return $this;
    }

    /**
     * Getter method for 'users_citizen_identification'
     *
     * @return string|null
     */
    public function getUsersCitizenIdentification(): ?string
    {
        return $this->users_citizen_identification;
    }

    /**
     * Setter method for 'users_citizen_identification'
     *
     * @param string|null $users_citizen_identification
     *
     * @return Users
     */
    public function setUsersCitizenIdentification(?string $users_citizen_identification = null): Users
    {
        $this->users_citizen_identification = $users_citizen_identification;

        return $this;
    }

    /**
     * Getter method for 'users_name'
     *
     * @return string|null
     */
    public function getUsersName(): ?string
    {
        return $this->users_name;
    }

    /**
     * Setter method for 'users_name'
     *
     * @param string|null $users_name
     *
     * @return Users
     */
    public function setUsersName(?string $users_name = null): Users
    {
        $this->users_name = $users_name;

        return $this;
    }

    /**
     * Getter method for 'users_last_name'
     *
     * @return string|null
     */
    public function getUsersLastName(): ?string
    {
        return $this->users_last_name;
    }

    /**
     * Setter method for 'users_last_name'
     *
     * @param string|null $users_last_name
     *
     * @return Users
     */
    public function setUsersLastName(?string $users_last_name = null): Users
    {
        $this->users_last_name = $users_last_name;

        return $this;
    }

    /**
     * Getter method for 'users_nickname'
     *
     * @return string|null
     */
    public function getUsersNickname(): ?string
    {
        return $this->users_nickname;
    }

    /**
     * Setter method for 'users_nickname'
     *
     * @param string|null $users_nickname
     *
     * @return Users
     */
    public function setUsersNickname(?string $users_nickname = null): Users
    {
        $this->users_nickname = $users_nickname;

        return $this;
    }

    /**
     * Getter method for 'users_email'
     *
     * @return string|null
     */
    public function getUsersEmail(): ?string
    {
        return $this->users_email;
    }

    /**
     * Setter method for 'users_email'
     *
     * @param string|null $users_email
     *
     * @return Users
     */
    public function setUsersEmail(?string $users_email = null): Users
    {
        $this->users_email = $users_email;

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
     * @return Users
     */
    public function setUsersPassword(?string $users_password = null): Users
    {
        $this->users_password = $users_password;

        return $this;
    }

    /**
     * Getter method for 'users_activation_code'
     *
     * @return string|null
     */
    public function getUsersActivationCode(): ?string
    {
        return $this->users_activation_code;
    }

    /**
     * Setter method for 'users_activation_code'
     *
     * @param string|null $users_activation_code
     *
     * @return Users
     */
    public function setUsersActivationCode(?string $users_activation_code = null): Users
    {
        $this->users_activation_code = $users_activation_code;

        return $this;
    }

    /**
     * Getter method for 'users_recovery_code'
     *
     * @return string|null
     */
    public function getUsersRecoveryCode(): ?string
    {
        return $this->users_recovery_code;
    }

    /**
     * Setter method for 'users_recovery_code'
     *
     * @param string|null $users_recovery_code
     *
     * @return Users
     */
    public function setUsersRecoveryCode(?string $users_recovery_code = null): Users
    {
        $this->users_recovery_code = $users_recovery_code;

        return $this;
    }

    /**
     * Getter method for 'users_code'
     *
     * @return string|null
     */
    public function getUsersCode(): ?string
    {
        return $this->users_code;
    }

    /**
     * Setter method for 'users_code'
     *
     * @param string|null $users_code
     *
     * @return Users
     */
    public function setUsersCode(?string $users_code = null): Users
    {
        $this->users_code = $users_code;

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
     * @return Users
     */
    public function setUsers2fa(?int $users_2fa = null): Users
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
     * @return Users
     */
    public function setUsers2faSecret(?string $users_2fa_secret = null): Users
    {
        $this->users_2fa_secret = $users_2fa_secret;

        return $this;
    }
}
