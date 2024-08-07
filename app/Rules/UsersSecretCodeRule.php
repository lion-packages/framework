<?php

declare(strict_types=1);

namespace App\Rules;

use Lion\Route\Helpers\Rules;
use Lion\Route\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'users_secret_code' property
 *
 * @property string $field [field for 'users_secret_code']
 * @property string $desc [description for 'users_secret_code']
 * @property string $value [value for 'users_secret_code']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
 *
 * @package App\Rules
 */
class UsersSecretCodeRule extends Rules implements RulesInterface
{
    /**
     * [field for 'users_secret_code']
     *
     * @var string $field
     */
    public string $field = 'users_secret_code';

    /**
     * [description for 'users_secret_code']
     *
     * @var string $desc
     */
    public string $desc = 'users_secret_code';

    /**
     * [value for 'users_secret_code']
     *
     * @var string $value;
     */
    public string $value = 'users_secret_code';

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
                ->message('the "users_secret_code" property is required');
        });
    }
}
