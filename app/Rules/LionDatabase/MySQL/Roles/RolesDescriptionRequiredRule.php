<?php

declare(strict_types=1);

namespace App\Rules\LionDatabase\MySQL\Roles;

use Lion\Bundle\Helpers\Rules;
use Lion\Bundle\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'roles_description' property
 *
 * @property string $field [field for 'roles_description']
 * @property string $desc [description for 'roles_description']
 * @property string $value [value for 'roles_description']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
 *
 * @package App\Rules\LionDatabase\MySQL\Roles
 */
class RolesDescriptionRequiredRule extends Rules implements RulesInterface
{
    /**
     * [field for 'roles_description']
     *
     * @var string $field
     */
    public string $field = 'roles_description';

    /**
     * [description for 'roles_description']
     *
     * @var string $desc
     */
    public string $desc = '';

    /**
     * [value for 'roles_description']
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
                ->message('the "roles_description" property is required');
        });
    }
}
