<?php

declare(strict_types=1);

namespace App\Rules\LionDatabase\MySQL\Users;

use Lion\Bundle\Helpers\Rules;
use Lion\Bundle\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'users_activation_code' property
 *
 * @property string $field [field for 'users_activation_code']
 * @property string $desc [description for 'users_activation_code']
 * @property string $value [value for 'users_activation_code']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
 *
 * @package App\Rules\LionDatabase\MySQL\Users
 */
class UsersActivationCodeRequiredRule extends Rules implements RulesInterface
{
    /**
     * [field for 'users_activation_code']
     *
     * @var string $field
     */
    public string $field = 'users_activation_code';

    /**
     * [description for 'users_activation_code']
     *
     * @var string $desc
     */
    public string $desc = '';

    /**
     * [value for 'users_activation_code']
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
                ->message('the "users_activation_code" property is required');
        });
    }
}
