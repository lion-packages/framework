<?php

declare(strict_types=1);

namespace Tests\Global\App\Exceptions;

use App\Exceptions\AuthenticationException;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;

class AuthenticationExceptionTest extends Test
{
    public function testAuthenticationException(): void
    {
        $this
            ->exception(AuthenticationException::class)
            ->exceptionMessage('ERR')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::HTTP_UNAUTHORIZED)
            ->expectLionException();
    }
}
