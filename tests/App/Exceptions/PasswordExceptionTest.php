<?php

declare(strict_types=1);

namespace Tests\App\Exceptions;

use App\Exceptions\PasswordException;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;

class PasswordExceptionTest extends Test
{
    /**
     * @throws Exception
     * @throws PasswordException
     */
    #[Testing]
    public function passwordException(): void
    {
        $this
            ->exception(PasswordException::class)
            ->exceptionMessage('ERR')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException();
    }
}
