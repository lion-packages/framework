<?php

declare(strict_types=1);

namespace App\Rules;

use Lion\Bundle\Helpers\Rules;
use Lion\Bundle\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'users_password_confirm' property
 *
 * @property string $field [field for 'users_password_confirm']
 * @property string $desc [description for 'users_password_confirm']
 * @property string $value [value for 'users_password_confirm']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
 *
 * @package App\Rules
 */
class UsersPasswordConfirmRule extends Rules implements RulesInterface
{
    /**
     * [field for 'users_password_confirm']
     *
     * @var string $field
     */
    public string $field = 'users_password_confirm';

    /**
     * [description for 'users_password_confirm']
     *
     * @var string $desc
     */
    public string $desc = 'users_password_confirm';

    /**
     * [value for 'users_password_confirm']
     *
     * @var string $value;
     */
    public string $value = 'users_password_confirm';

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
        $this->validate(function (Validator $validator) {
            $validator
                ->rule('required', $this->field)
                ->message('the "users_password_confirm" property is required');
        });
    }
}
