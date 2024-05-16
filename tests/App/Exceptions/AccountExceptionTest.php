<?php

declare(strict_types=1);

namespace Tests\App\Exceptions;

use App\Exceptions\AccountException;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;

class AccountExceptionTest extends Test
{
    public function testAuthenticationException(): void
    {
        $this
            ->exception(AccountException::class)
            ->exceptionMessage('ERR')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::HTTP_UNAUTHORIZED)
            ->expectLionException();
    }
}
