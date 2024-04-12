<?php

declare(strict_types=1);

namespace Tests\App\Exceptions;

use App\Exceptions\AuthenticationException;
use Lion\Request\Request;
use Lion\Test\Test;

class AuthenticationExceptionTest extends Test
{
    public function testAuthenticationException(): void
    {
        $this->expectException(AuthenticationException::class);

        throw new AuthenticationException('ERR', Request::HTTP_UNAUTHORIZED);
    }
}
