<?php

declare(strict_types=1);

namespace Tests\App\Exceptions;

use App\Exceptions\AuthenticationException;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;

class AuthenticationExceptionTest extends Test
{
    /**
     * @throws Exception
     * @throws AuthenticationException
     */
    public function testAuthenticationException(): void
    {
        $this
            ->exception(AuthenticationException::class)
            ->exceptionMessage('ERR')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException();
    }
}
