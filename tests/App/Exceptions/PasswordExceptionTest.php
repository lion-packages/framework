<?php

declare(strict_types=1);

namespace Tests\App\Exceptions;

use App\Exceptions\PasswordException;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;

class PasswordExceptionTest extends Test
{
    const MESSAGE = 'ERR';

    public function testPasswordException(): void
    {
        $this
            ->exception(PasswordException::class)
            ->exceptionMessage('ERR')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::HTTP_UNAUTHORIZED)
            ->expectLionException();
    }
}
