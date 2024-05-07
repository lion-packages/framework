<?php

declare(strict_types=1);

namespace App\Rules\LionDatabase\MySQL\DocumentTypes;

use Lion\Bundle\Helpers\Rules;
use Lion\Bundle\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'iddocument_types' property
 *
 * @property string $field [field for 'iddocument_types']
 * @property string $desc [description for 'iddocument_types']
 * @property string $value [value for 'iddocument_types']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
 *
 * @package App\Rules\LionDatabase\MySQL\DocumentTypes
 */
class IddocumentTypesRule extends Rules implements RulesInterface
{
    /**
     * [field for 'iddocument_types']
     *
     * @var string $field
     */
    public string $field = 'iddocument_types';

    /**
     * [description for 'iddocument_types']
     *
     * @var string $desc
     */
    public string $desc = '';

    /**
     * [value for 'iddocument_types']
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
        $this->validate(function (Validator $validator): void {
            $validator
                ->rule('required', $this->field)
                ->message('the "iddocument_types" property is required');
        });
    }
}
