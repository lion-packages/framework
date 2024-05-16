<?php

declare(strict_types=1);

namespace App\Exceptions;

use JsonSerializable;
use Lion\Bundle\Support\Exceptions\ExceptionSupport;
use Lion\Bundle\Traits\ExceptionsTrait;

/**
 * Exception handling for user passwords
 *
 * @package App\Exceptions
 */
class PasswordException extends ExceptionSupport implements JsonSerializable
{
    use ExceptionsTrait;
}
