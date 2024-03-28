<?php

declare(strict_types=1);

namespace App\Rules\LionDatabase\MySQL\DocumentTypes;

use Lion\Bundle\Helpers\Rules;
use Lion\Bundle\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'document_types_name' property
 *
 * @property string $field [field for 'document_types_name']
 * @property string $desc [description for 'document_types_name']
 * @property string $value [value for 'document_types_name']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
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
     * @var string $value;
     */
    public string $value = '';

    /**
     * [Defines whether the column is optional for postman collections]
     *
     * @var bool $disabled;
     */
    public bool $disabled = false;

    /**
     * {@inheritdoc}
     */
    public function passes(): void
    {
        $this->validate(function(Validator $validator) {
            $validator
                ->rule('required', $this->field)
                ->message('the "document_types_name" property is required');
        });
    }
}
