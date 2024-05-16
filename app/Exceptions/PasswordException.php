<?php

declare(strict_types=1);

namespace App\Exceptions;

use Lion\Exceptions\Exception;
use Lion\Exceptions\Interfaces\ExceptionInterface;
use Lion\Exceptions\Traits\ExceptionTrait;

/**
 * Exception handling for user passwords
 *
 * @package App\Exceptions
 */
class PasswordException extends Exception implements ExceptionInterface
{
    use ExceptionTrait;
}
