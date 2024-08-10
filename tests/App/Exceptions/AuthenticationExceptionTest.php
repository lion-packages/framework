<?php

declare(strict_types=1);

namespace Tests\App\Exceptions;

use App\Exceptions\AuthenticationException;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;

class AuthenticationExceptionTest extends Test
{
    /**
     * @throws Exception
     * @throws AuthenticationException
     */
    #[Testing]
    public function authenticationException(): void
    {
        $this
            ->exception(AuthenticationException::class)
            ->exceptionMessage('ERR')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException();
    }
}
