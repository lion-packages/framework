<?php

declare(strict_types=1);

namespace App\Rules\LionDatabase\MySQL\Roles;

use Lion\Route\Helpers\Rules;
use Lion\Route\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'idroles' property
 *
 * @property string $field [field for 'idroles']
 * @property string $desc [description for 'idroles']
 * @property string $value [value for 'idroles']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
 *
 * @package App\Rules\LionDatabase\MySQL\Roles
 */
class IdrolesRule extends Rules implements RulesInterface
{
    /**
     * [field for 'idroles']
     *
     * @var string $field
     */
    public string $field = 'idroles';

    /**
     * [description for 'idroles']
     *
     * @var string $desc
     */
    public string $desc = '';

    /**
     * [value for 'idroles']
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
                ->message('the "idroles" property is required');
        });
    }
}
