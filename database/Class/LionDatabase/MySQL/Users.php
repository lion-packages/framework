<?php

declare(strict_types=1);

namespace Database\Class\LionDatabase\MySQL;

use JsonSerializable;
use Lion\Bundle\Interface\CapsuleInterface;

class Users implements CapsuleInterface, JsonSerializable
{
	private ?int $idusers = null;
	private ?int $idroles = null;
	private ?int $iddocument_types = null;
	private ?string $users_name = null;
	private ?string $users_last_name = null;
	private ?string $users_email = null;
	private ?string $users_password = null;
	private ?string $users_code = null;

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

	public function getIdusers(): ?int
	{
		return $this->idusers;
	}

	public function setIdusers(?int $idusers): Users
	{
		$this->idusers = $idusers;

		return $this;
	}

	public function getIdroles(): ?int
	{
		return $this->idroles;
	}

	public function setIdroles(?int $idroles): Users
	{
		$this->idroles = $idroles;

		return $this;
	}

	public function getIddocumentTypes(): ?int
	{
		return $this->iddocument_types;
	}

	public function setIddocumentTypes(?int $iddocument_types): Users
	{
		$this->iddocument_types = $iddocument_types;

		return $this;
	}

	public function getUsersName(): ?string
	{
		return $this->users_name;
	}

	public function setUsersName(?string $users_name): Users
	{
		$this->users_name = $users_name;

		return $this;
	}

	public function getUsersLastName(): ?string
	{
		return $this->users_last_name;
	}

	public function setUsersLastName(?string $users_last_name): Users
	{
		$this->users_last_name = $users_last_name;

		return $this;
	}

	public function getUsersEmail(): ?string
	{
		return $this->users_email;
	}

	public function setUsersEmail(?string $users_email): Users
	{
		$this->users_email = $users_email;

		return $this;
	}

	public function getUsersPassword(): ?string
	{
		return $this->users_password;
	}

	public function setUsersPassword(?string $users_password): Users
	{
		$this->users_password = $users_password;

		return $this;
	}

	public function getUsersCode(): ?string
	{
		return $this->users_code;
	}

	public function setUsersCode(?string $users_code): Users
	{
		$this->users_code = $users_code;

		return $this;
	}
}