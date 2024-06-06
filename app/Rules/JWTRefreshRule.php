<?php

declare(strict_types=1);

namespace App\Rules;

use Lion\Route\Helpers\Rules;
use Lion\Route\Interface\RulesInterface;
use Valitron\Validator;

/**
 * Rule defined for the 'jwt_refresh' property
 *
 * @property string $field [field for 'jwt_refresh']
 * @property string $desc [description for 'jwt_refresh']
 * @property string $value [value for 'jwt_refresh']
 * @property bool $disabled [Defines whether the column is optional for postman
 * collections]
 *
 * @package App\Rules
 */
class JWTRefreshRule extends Rules implements RulesInterface
{
    /**
     * [field for 'jwt_refresh']
     *
     * @var string $field
     */
    public string $field = 'jwt_refresh';

    /**
     * [description for 'jwt_refresh']
     *
     * @var string $desc
     */
    public string $desc = 'jwt_refresh';

    /**
     * [value for 'jwt_refresh']
     *
     * @var string $value;
     */
    public string $value = 'jwt_refresh';

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
                ->message('the "jwt_refresh" property is required');
        });
    }
}
