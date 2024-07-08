<?php

declare(strict_types=1);

namespace App\Exceptions;

use Lion\Exceptions\Exception;
use Lion\Exceptions\Interfaces\ExceptionInterface;
use Lion\Exceptions\Traits\ExceptionTrait;

/**
 * Exception to control web system processes
 *
 * @package App\Exceptions
 */
class ProcessException extends Exception implements ExceptionInterface
{
    use ExceptionTrait;
}
