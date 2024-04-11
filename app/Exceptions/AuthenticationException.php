<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Http\Services\LionDatabase\MySQL\LoginService;
use Exception;
use JsonSerializable;

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
        return response(LoginService::AUTH_ERROR, $this->getMessage(), $this->getCode());
    }
}
