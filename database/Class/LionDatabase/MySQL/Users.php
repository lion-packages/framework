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
 * @property string $users_code [Property for users_code]
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
	 * [Property for users_code]
	 *
	 * @var string|null $users_code
	 */
	private ?string $users_code = null;

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
			->setIdusers(request->idusers ?? null)
			->setIdroles(request->idroles ?? null)
			->setIddocumentTypes(request->iddocument_types ?? null)
			->setUsersCitizenIdentification(request->users_citizen_identification ?? null)
			->setUsersName(request->users_name ?? null)
			->setUsersLastName(request->users_last_name ?? null)
			->setUsersNickname(request->users_nickname ?? null)
			->setUsersEmail(request->users_email ?? null)
			->setUsersPassword(request->users_password ?? null)
			->setUsersCode(request->users_code ?? null);

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
}
