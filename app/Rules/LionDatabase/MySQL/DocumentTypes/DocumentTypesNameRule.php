<?php

declare(strict_types=1);

namespace App\Rules\LionDatabase\MySQL\DocumentTypes;

use Lion\Bundle\Helpers\Rules;
use Lion\Bundle\Interface\RulesInterface;
use Valitron\Validator;

/**
 * [Rule defined for the 'document_types_name' property]
 *
 * @package App\Rules\LionDatabase\MySQL\DocumentTypes
 */
class DocumentTypesNameRule extends Rules implements RulesInterface
{
	/**
	 * [field for 'document_types_name']
	 *
	 * @var string $field
	 */
	public string $field = 'document_types_name';

	/**
	 * [description for 'document_types_name']
	 *
	 * @var string $desc
	 */
	public string $desc = '';

	/**
	 * [value for 'document_types_name']
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