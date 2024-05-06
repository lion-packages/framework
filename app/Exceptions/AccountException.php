<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use JsonSerializable;

/**
 * Exception handling for user accounts
 *
 * @package App\Exceptions
 */
class AccountException extends Exception implements JsonSerializable
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): mixed
    {
        return error($this->getMessage(), $this->getCode());
    }
}
