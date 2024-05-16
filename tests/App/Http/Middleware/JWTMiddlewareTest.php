<?php

declare(strict_types=1);

namespace Tests\App\Http\Middleware;

use App\Http\Middleware\JWTMiddleware;
use Lion\Bundle\Exceptions\MiddlewareException;
use Lion\Dependency\Injection\Container;
use Lion\Request\Request;
use Lion\Security\RSA;
use Lion\Test\Test;
use Tests\Providers\AuthJwtProviderTrait;

class JWTMiddlewareTest extends Test
{
    use AuthJwtProviderTrait;

    const MESSAGE = 'ERR';

    private JWTMiddleware $jWTMiddleware;

    private string $users_code;

    protected function setUp(): void
    {
        $this->jWTMiddleware = (new Container())
            ->injectDependencies(new JWTMiddleware());

        $this->users_code = uniqid('code-');

        $this->initReflection($this->jWTMiddleware);
    }

    protected function tearDown(): void
    {
        $this->rmdirRecursively(env('RSA_URL_PATH') . "{$this->users_code}/");
    }

    public function testInitRSA(): void
    {
        $path = str->of(env('RSA_URL_PATH'))->replace('../', '')->get();

        $this->getPrivateMethod('initRSA', [$path]);

        /** @var RSA $rsa */
        $rsa = $this->getPrivateProperty('rsa');

        $this->assertSame($path, $rsa->getUrlPath());
    }

    public function testValidateSessionIsError(): void
    {
        $this->expectException(MiddlewareException::class);
        $this->expectExceptionMessage(self::MESSAGE);
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);

        $this->getPrivateMethod('validateSession', [error(self::MESSAGE)]);
    }

    public function testValidateSessionNotSession(): void
    {
        $this->expectException(MiddlewareException::class);
        $this->expectExceptionMessage('undefined session');
        $this->expectExceptionCode(Request::HTTP_FORBIDDEN);

        $this->getPrivateMethod('validateSession', [success()]);
    }

    public function testExistence(): void
    {
        $this->expectException(MiddlewareException::class);
        $this->expectExceptionMessage('the JWT does not exist');
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);

        $this->getPrivateMethod('existence');
    }

    public function testAuthorizeWithoutSignatureInvalidJWT(): void
    {
        $this->expectException(MiddlewareException::class);
        $this->expectExceptionMessage('invalid JWT [AUTH-1]');
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);

        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . uniqid() . '.' . uniqid();

        $this->jWTMiddleware->authorizeWithoutSignature();
    }

    public function testAuthorizeWithoutSignatureWithoutUsersCode(): void
    {
        $this->expectException(MiddlewareException::class);
        $this->expectExceptionMessage('invalid JWT [AUTH-2]');
        $this->expectExceptionCode(Request::HTTP_FORBIDDEN);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization();

        $this->jWTMiddleware->authorizeWithoutSignature();
    }

    public function testAuthorizeWithoutSignaturePathNotExist(): void
    {
        $this->expectException(MiddlewareException::class);
        $this->expectExceptionMessage('invalid JWT [AUTH-3]');
        $this->expectExceptionCode(Request::HTTP_FORBIDDEN);

        $this->generateKeys($this->users_code);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getCustomAuthorization("{$this->users_code}/", [
            'users_code' => $this->users_code
        ]);

        $this->rmdirRecursively(env('RSA_URL_PATH') . "{$this->users_code}/");

        $this->jWTMiddleware->authorizeWithoutSignature();
    }

    public function testAuthorizeWithoutSignatureNotAuthorize(): void
    {
        $this->expectException(MiddlewareException::class);
        $this->expectExceptionMessage('user not logged in, you must log in');
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);

        $this->generateKeys($this->users_code);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getCustomAuthorization("{$this->users_code}/", [
            'session' => false,
            'users_code' => $this->users_code
        ]);

        $this->jWTMiddleware->authorizeWithoutSignature();
    }

    public function testAuthorize(): void
    {
        $this->expectException(MiddlewareException::class);
        $this->expectExceptionMessage('user not logged in, you must log in');
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization(['session' => false]);

        $this->jWTMiddleware->authorize();
    }

    public function testNotAuthorize(): void
    {
        $this->expectException(MiddlewareException::class);
        $this->expectExceptionMessage('user in session, you must close the session');
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization();

        $this->jWTMiddleware->notAuthorize();
    }
}
