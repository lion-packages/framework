<?php

declare(strict_types=1);

namespace App\Rules\LionDatabase\MySQL\Users;

use Lion\Bundle\Helpers\Rules;
use Lion\Bundle\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'idusers' property
 *
 * @property string $field [field for 'idusers']
 * @property string $desc [description for 'idusers']
 * @property string $value [value for 'idusers']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
 *
 * @package App\Rules\LionDatabase\MySQL\Users
 */
class IdusersRule extends Rules implements RulesInterface
{
    /**
     * [field for 'idusers']
     *
     * @var string $field
     */
    public string $field = 'idusers';

    /**
     * [description for 'idusers']
     *
     * @var string $desc
     */
    public string $desc = '';

    /**
     * [value for 'idusers']
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
                ->message('the "idusers" property is required');
        });
    }
}
