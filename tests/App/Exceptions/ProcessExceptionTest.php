<?php

declare(strict_types=1);

namespace Tests\App\Exceptions;

use App\Exceptions\ProcessException;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;

class ProcessExceptionTest extends Test
{
    /**
     * @throws Exception
     * @throws ProcessException
     * @throws Exception
     */
    #[Testing]
    public function processException(): void
    {
        $this
            ->exception(ProcessException::class)
            ->exceptionMessage('ERR')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException();
    }
}
