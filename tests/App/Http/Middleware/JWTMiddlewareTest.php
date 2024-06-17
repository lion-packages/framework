<?php

declare(strict_types=1);

namespace Tests\App\Http\Middleware;

use App\Http\Middleware\JWTMiddleware;
use Lion\Bundle\Exceptions\MiddlewareException;
use Lion\Dependency\Injection\Container;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\RSA;
use Lion\Test\Test;
use Tests\Providers\AuthJwtProviderTrait;

class JWTMiddlewareTest extends Test
{
    use AuthJwtProviderTrait;

    const string MESSAGE = 'ERR';

    private JWTMiddleware $jWTMiddleware;

    private string $users_code;

    protected function setUp(): void
    {
        $this->jWTMiddleware = (new Container())->injectDependencies(new JWTMiddleware());

        $this->users_code = uniqid('code-');

        $this->initReflection($this->jWTMiddleware);
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    public function testInitRSA(): void
    {
        $this->getPrivateMethod('initRSA', [env('RSA_URL_PATH')]);

        /** @var RSA $rsa */
        $rsa = $this->getPrivateProperty('rsa');

        $this->assertSame(env('RSA_URL_PATH'), $rsa->getUrlPath());
    }

    /**
     * @throws Exception
     * @throws MiddlewareException
     */
    public function testValidateSessionIsError(): void
    {
        $this
            ->exception(MiddlewareException::class)
            ->exceptionMessage(self::MESSAGE)
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $this->getPrivateMethod('validateSession', [error(self::MESSAGE)]);
            });
    }

    /**
     * @throws Exception
     * @throws MiddlewareException
     */
    public function testValidateSessionNotSession(): void
    {
        $this
            ->exception(MiddlewareException::class)
            ->exceptionMessage('undefined session')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::FORBIDDEN)
            ->expectLionException(function (): void {
                $this->getPrivateMethod('validateSession', [success()]);
            });
    }

    /**
     * @throws Exception
     * @throws MiddlewareException
     */
    public function testExistence(): void
    {
        $this
            ->exception(MiddlewareException::class)
            ->exceptionMessage('the JWT does not exist')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $this->getPrivateMethod('existence');
            });
    }

    /**
     * @throws Exception
     * @throws MiddlewareException
     */
    public function testAuthorizeWithoutSignatureInvalidJWT(): void
    {
        $this
            ->exception(MiddlewareException::class)
            ->exceptionMessage('invalid JWT [AUTH-1]')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . uniqid() . '.' . uniqid();

                $this->jWTMiddleware->authorizeWithoutSignature();
            });
    }

    /**
     * @throws Exception
     * @throws MiddlewareException
     */
    public function testAuthorizeWithoutSignatureWithoutUsersCode(): void
    {
        $this
            ->exception(MiddlewareException::class)
            ->exceptionMessage('invalid JWT [AUTH-2]')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::FORBIDDEN)
            ->expectLionException(function (): void {
                $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization();

                $this->jWTMiddleware->authorizeWithoutSignature();
            });
    }

    /**
     * @throws Exception
     * @throws MiddlewareException
     */
    public function testAuthorizeWithoutSignaturePathNotExist(): void
    {
        $this
            ->exception(MiddlewareException::class)
            ->exceptionMessage('invalid JWT [AUTH-3]')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::FORBIDDEN)
            ->expectLionException(function (): void {
                $this->generateKeys($this->users_code);

                $_SERVER['HTTP_AUTHORIZATION'] = $this->getCustomAuthorization("{$this->users_code}/", [
                    'users_code' => $this->users_code
                ]);

                $this->rmdirRecursively(env('RSA_URL_PATH') . "{$this->users_code}/");

                $this->jWTMiddleware->authorizeWithoutSignature();
            });
    }

    /**
     * @throws Exception
     * @throws MiddlewareException
     */
    public function testAuthorizeWithoutSignatureNotAuthorize(): void
    {
        $this
            ->exception(MiddlewareException::class)
            ->exceptionMessage('user not logged in, you must log in')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $this->generateKeys($this->users_code);

                $_SERVER['HTTP_AUTHORIZATION'] = $this->getCustomAuthorization("{$this->users_code}/", [
                    'session' => false,
                    'users_code' => $this->users_code
                ]);

                $this->jWTMiddleware->authorizeWithoutSignature();
            });
    }

    /**
     * @throws Exception
     * @throws MiddlewareException
     */
    public function testAuthorize(): void
    {
        $this
            ->exception(MiddlewareException::class)
            ->exceptionMessage('user not logged in, you must log in')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization(['session' => false]);

                $this->jWTMiddleware->authorize();
            });
    }

    /**
     * @throws Exception
     * @throws MiddlewareException
     */
    public function testNotAuthorize(): void
    {
        $this
            ->exception(MiddlewareException::class)
            ->exceptionMessage('user in session, you must close the session')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization();

                $this->jWTMiddleware->notAuthorize();
            });
    }
}
