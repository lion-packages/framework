<?php

declare(strict_types=1);

namespace App\Rules\LionDatabase\MySQL\Users;

use Lion\Bundle\Helpers\Rules;
use Lion\Bundle\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'users_nickname' property
 *
 * @property string $field [field for 'users_nickname']
 * @property string $desc [description for 'users_nickname']
 * @property string $value [value for 'users_nickname']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
 *
 * @package App\Rules\LionDatabase\MySQL\Users
 */
class UsersNicknameOptionalRule extends Rules implements RulesInterface
{
    /**
     * [field for 'users_nickname']
     *
     * @var string $field
     */
    public string $field = 'users_nickname';

    /**
     * [description for 'users_nickname']
     *
     * @var string $desc
     */
    public string $desc = '';

    /**
     * [value for 'users_nickname']
     *
     * @var string $value;
     */
    public string $value = '';

    /**
     * [Defines whether the column is optional for postman collections]
     *
     * @var bool $disabled;
     */
    public bool $disabled = true;
 
    /**
     * {@inheritdoc}
     */
    public function passes(): void
    {
        $this->validate(function (Validator $validator): void {
            $validator
                ->rule('optional', $this->field)
                ->message('the "users_nickname" property is optional');
        });
    }
}
