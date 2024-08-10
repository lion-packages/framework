<?php

declare(strict_types=1);

namespace Database\Class\LionDatabase\MySQL;

use Lion\Bundle\Interface\CapsuleInterface;
use Lion\Bundle\Traits\CapsuleTrait;

/**
 * Capsule for the 'Roles' entity
 *
 * @property string $entity [Entity name]
 * @property int|null $idroles [Property for idroles]
 * @property string|null $roles_name [Property for roles_name]
 * @property string|null $roles_description [Property for roles_description]
 *
 * @package Database\Class\LionDatabase\MySQL
 */
class Roles implements CapsuleInterface
{
	use CapsuleTrait;

	/**
     * [Entity name]
     *
     * @var string $entity
     */
    private string $entity = 'roles';

	/**
	 * [Property for idroles]
	 *
	 * @var int|null $idroles
	 */
	private ?int $idroles = null;

	/**
	 * [Property for roles_name]
	 *
	 * @var string|null $roles_name
	 */
	private ?string $roles_name = null;

	/**
	 * [Property for roles_description]
	 *
	 * @var string|null $roles_description
	 */
	private ?string $roles_description = null;

	/**
	 * {@inheritdoc}
	 * */
	public function capsule(): Roles
	{
		$this
			->setIdroles(request('idroles'))
			->setRolesName(request('roles_name'))
			->setRolesDescription(request('roles_description'));

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
     * @return Roles
     */
    public function setIdroles(?int $idroles = null): Roles
    {
        $this->idroles = $idroles;

        return $this;
    }

    /**
     * Getter method for 'roles_name'
     *
     * @return string|null
     */
    public function getRolesName(): ?string
    {
        return $this->roles_name;
    }

    /**
     * Setter method for 'roles_name'
     *
     * @param string|null $roles_name
     *
     * @return Roles
     */
    public function setRolesName(?string $roles_name = null): Roles
    {
        $this->roles_name = $roles_name;

        return $this;
    }

    /**
     * Getter method for 'roles_description'
     *
     * @return string|null
     */
    public function getRolesDescription(): ?string
    {
        return $this->roles_description;
    }

    /**
     * Setter method for 'roles_description'
     *
     * @param string|null $roles_description
     *
     * @return Roles
     */
    public function setRolesDescription(?string $roles_description = null): Roles
    {
        $this->roles_description = $roles_description;

        return $this;
    }
}
