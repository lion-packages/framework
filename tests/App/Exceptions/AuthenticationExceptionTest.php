<?php

declare(strict_types=1);

namespace Tests\App\Exceptions;

use App\Exceptions\AuthenticationException;
use Lion\Request\Request;
use Lion\Test\Test;

class AuthenticationExceptionTest extends Test
{
    const MESSAGE = 'ERR';

    public function testAuthenticationException(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);
        $this->expectExceptionMessage(self::MESSAGE);

        throw new AuthenticationException(self::MESSAGE, Request::HTTP_UNAUTHORIZED);
    }
}
