<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use JsonSerializable;
use Lion\Request\Response;

/**
 * Exception handling for user authentication
 *
 * @package App\Exceptions
 */
class AuthenticationException extends Exception implements JsonSerializable
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): mixed
    {
        return response(Response::SESSION_ERROR, $this->getMessage(), $this->getCode());
    }
}
