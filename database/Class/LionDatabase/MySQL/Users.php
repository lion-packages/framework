<?php

declare(strict_types=1);

namespace Database\Class\LionDatabase\MySQL;

use JsonSerializable;
use Lion\Bundle\Interface\CapsuleInterface;

class Users implements CapsuleInterface, JsonSerializable
{
	private ?int $idusers = null;
	private ?int $idroles = null;
	private ?int $iddocumentTypes = null;
	private ?string $usersName = null;
	private ?string $usersLastName = null;
	private ?string $usersEmail = null;
	private ?string $usersPassword = null;
	private ?string $usersCode = null;

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
			->setIddocumentTypes(request->iddocumentTypes ?? null)
			->setUsersName(request->usersName ?? null)
			->setUsersLastName(request->usersLastName ?? null)
			->setUsersEmail(request->usersEmail ?? null)
			->setUsersPassword(request->usersPassword ?? null)
			->setUsersCode(request->usersCode ?? null);

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
		return $this->iddocumentTypes;
	}

	public function setIddocumentTypes(?int $iddocumentTypes): Users
	{
		$this->iddocumentTypes = $iddocumentTypes;

		return $this;
	}

	public function getUsersName(): ?string
	{
		return $this->usersName;
	}

	public function setUsersName(?string $usersName): Users
	{
		$this->usersName = $usersName;

		return $this;
	}

	public function getUsersLastName(): ?string
	{
		return $this->usersLastName;
	}

	public function setUsersLastName(?string $usersLastName): Users
	{
		$this->usersLastName = $usersLastName;

		return $this;
	}

	public function getUsersEmail(): ?string
	{
		return $this->usersEmail;
	}

	public function setUsersEmail(?string $usersEmail): Users
	{
		$this->usersEmail = $usersEmail;

		return $this;
	}

	public function getUsersPassword(): ?string
	{
		return $this->usersPassword;
	}

	public function setUsersPassword(?string $usersPassword): Users
	{
		$this->usersPassword = $usersPassword;

		return $this;
	}

	public function getUsersCode(): ?string
	{
		return $this->usersCode;
	}

	public function setUsersCode(?string $usersCode): Users
	{
		$this->usersCode = $usersCode;

		return $this;
	}
}