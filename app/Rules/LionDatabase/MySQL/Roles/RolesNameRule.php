<?php

declare(strict_types=1);

namespace App\Rules\LionDatabase\MySQL\Roles;

use Lion\Route\Helpers\Rules;
use Lion\Route\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'roles_name' property
 *
 * @property string $field [field for 'roles_name']
 * @property string $desc [description for 'roles_name']
 * @property string $value [value for 'roles_name']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
 *
 * @package App\Rules\LionDatabase\MySQL\Roles
 */
class RolesNameRule extends Rules implements RulesInterface
{
    /**
     * [field for 'roles_name']
     *
     * @var string $field
     */
    public string $field = 'roles_name';

    /**
     * [description for 'roles_name']
     *
     * @var string $desc
     */
    public string $desc = '';

    /**
     * [value for 'roles_name']
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
                ->message('the "roles_name" property is required');
        });
    }
}
