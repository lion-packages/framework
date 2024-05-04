<?php

declare(strict_types=1);

namespace Tests\App\Exceptions;

use App\Exceptions\AccountException;
use Lion\Request\Request;
use Lion\Test\Test;

class AccountExceptionTest extends Test
{
    const MESSAGE = 'ERR';

    public function testAuthenticationException(): void
    {
        $this->expectException(AccountException::class);
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);
        $this->expectExceptionMessage(self::MESSAGE);

        throw new AccountException(self::MESSAGE, Request::HTTP_UNAUTHORIZED);
    }
}
