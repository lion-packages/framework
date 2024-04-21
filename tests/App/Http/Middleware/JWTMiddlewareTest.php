<?php

declare(strict_types=1);

namespace Tests\App\Http\Middleware;

use Exception;
use Lion\Request\Request;
use Lion\Request\Response;
use Lion\Route\Route;
use Lion\Test\Test;

class JWTMiddlewareTest extends Test
{
    const URI = 'http://127.0.0.1:8000/api/users';

    public function testAuthorizeNotExistence(): void
    {
        $exception = $this->getExceptionFromApi(function () {
            fetch(Route::GET, self::URI);
        });

        $this->assertInstanceOf(Exception::class, $exception);

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Request::HTTP_UNAUTHORIZED,
            'status' => Response::SESSION_ERROR,
            'message' => 'the JWT does not exist'
        ]);
    }
}
