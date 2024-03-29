<?php

declare(strict_types=1);

namespace App\Rules\LionDatabase\MySQL\Users;

use Lion\Bundle\Helpers\Rules;
use Lion\Bundle\Interface\RulesInterface;
use Valitron\Validator;

/**
 * [Rule defined for the 'users_password' property]
 *
 * @package App\Rules\LionDatabase\MySQL\Users
 */
class UsersPasswordRule extends Rules implements RulesInterface
{
	/**
	 * [field for 'users_password']
	 *
	 * @var string $field
	 */
	public string $field = 'users_password';

	/**
	 * [description for 'users_password']
	 *
	 * @var string $desc
	 */
	public string $desc = '';

	/**
	 * [value for 'users_password']
	 *
	 * @var string $value
	 */
	public string $value = '';

	/**
	 * [Defines whether the column is optional for postman collections]
	 *
	 * @var string $value
	 */
	public bool $disabled = false;

	/**
	 * {@inheritdoc}
	 * */
	public function passes(): void
	{
		$this->validate(function(Validator $validator) {
			$validator->rule('required', $this->field)->message('property is required');
		});
	}
}