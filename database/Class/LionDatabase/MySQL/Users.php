<?php

declare(strict_types=1);

namespace Database\Class\LionDatabase\MySQL;

use JsonSerializable;
use Lion\Bundle\Interface\CapsuleInterface;

class Users implements CapsuleInterface, JsonSerializable
{
	/**
	 * property for idusers
	 *
	 * @var int|null $idusers
	 */
	private ?int $idusers = null;

	/**
	 * property for idroles
	 *
	 * @var int|null $idroles
	 */
	private ?int $idroles = null;

	/**
	 * property for iddocument_types
	 *
	 * @var int|null $iddocument_types
	 */
	private ?int $iddocument_types = null;

	/**
	 * property for users_name
	 *
	 * @var string|null $users_name
	 */
	private ?string $users_name = null;

	/**
	 * property for users_last_name
	 *
	 * @var string|null $users_last_name
	 */
	private ?string $users_last_name = null;

	/**
	 * property for users_email
	 *
	 * @var string|null $users_email
	 */
	private ?string $users_email = null;

	/**
	 * property for users_password
	 *
	 * @var string|null $users_password
	 */
	private ?string $users_password = null;

	/**
	 * property for users_code
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
			->setUsersName(request->users_name ?? null)
			->setUsersLastName(request->users_last_name ?? null)
			->setUsersEmail(request->users_email ?? null)
			->setUsersPassword(request->users_password ?? null)
			->setUsersCode(request->users_code ?? null);

		return $this;
	}

	/**
	 * getter method for idusers
	 *
	 * @return int|null
	 */
	public function getIdusers(): ?int
	{
		return $this->idusers;
	}

	/**
	 * setter method for idusers
	 *
	 * @return Users
	 */
	public function setIdusers(?int $idusers): Users
	{
		$this->idusers = $idusers;

		return $this;
	}

	/**
	 * getter method for idroles
	 *
	 * @return int|null
	 */
	public function getIdroles(): ?int
	{
		return $this->idroles;
	}

	/**
	 * setter method for idroles
	 *
	 * @return Users
	 */
	public function setIdroles(?int $idroles): Users
	{
		$this->idroles = $idroles;

		return $this;
	}

	/**
	 * getter method for iddocument_types
	 *
	 * @return int|null
	 */
	public function getIddocumentTypes(): ?int
	{
		return $this->iddocument_types;
	}

	/**
	 * setter method for iddocument_types
	 *
	 * @return Users
	 */
	public function setIddocumentTypes(?int $iddocument_types): Users
	{
		$this->iddocument_types = $iddocument_types;

		return $this;
	}

	/**
	 * getter method for users_name
	 *
	 * @return string|null
	 */
	public function getUsersName(): ?string
	{
		return $this->users_name;
	}

	/**
	 * setter method for users_name
	 *
	 * @return Users
	 */
	public function setUsersName(?string $users_name): Users
	{
		$this->users_name = $users_name;

		return $this;
	}

	/**
	 * getter method for users_last_name
	 *
	 * @return string|null
	 */
	public function getUsersLastName(): ?string
	{
		return $this->users_last_name;
	}

	/**
	 * setter method for users_last_name
	 *
	 * @return Users
	 */
	public function setUsersLastName(?string $users_last_name): Users
	{
		$this->users_last_name = $users_last_name;

		return $this;
	}

	/**
	 * getter method for users_email
	 *
	 * @return string|null
	 */
	public function getUsersEmail(): ?string
	{
		return $this->users_email;
	}

	/**
	 * setter method for users_email
	 *
	 * @return Users
	 */
	public function setUsersEmail(?string $users_email): Users
	{
		$this->users_email = $users_email;

		return $this;
	}

	/**
	 * getter method for users_password
	 *
	 * @return string|null
	 */
	public function getUsersPassword(): ?string
	{
		return $this->users_password;
	}

	/**
	 * setter method for users_password
	 *
	 * @return Users
	 */
	public function setUsersPassword(?string $users_password): Users
	{
		$this->users_password = $users_password;

		return $this;
	}

	/**
	 * getter method for users_code
	 *
	 * @return string|null
	 */
	public function getUsersCode(): ?string
	{
		return $this->users_code;
	}

	/**
	 * setter method for users_code
	 *
	 * @return Users
	 */
	public function setUsersCode(?string $users_code): Users
	{
		$this->users_code = $users_code;

		return $this;
	}
}