<?php

declare(strict_types=1);

namespace App\Rules;

use Lion\Route\Helpers\Rules;
use Lion\Route\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'users_2fa_secret' property
 *
 * @property string $field [field for 'users_2fa_secret']
 * @property string $desc [description for 'users_2fa_secret']
 * @property string $value [value for 'users_2fa_secret']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
 *
 * @package App\Rules
 */
class Users2FASecretRule extends Rules implements RulesInterface
{
    /**
     * [field for 'users_2fa_secret']
     *
     * @var string $field
     */
    public string $field = 'users_2fa_secret';

    /**
     * [description for 'users_2fa_secret']
     *
     * @var string $desc
     */
    public string $desc = 'users_2fa_secret';

    /**
     * [value for 'users_2fa_secret']
     *
     * @var string $value;
     */
    public string $value = 'users_2fa_secret';

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
                ->message('the "users_2fa_secret" property is required');
        });
    }
}
