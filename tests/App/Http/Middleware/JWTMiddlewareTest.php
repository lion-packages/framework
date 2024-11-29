<?php

declare(strict_types=1);

namespace Tests\App\Http\Middleware;

use App\Http\Middleware\JWTMiddleware;
use Lion\Bundle\Exceptions\MiddlewareException;
use Lion\Exceptions\Exception;
use Lion\Files\Store;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\JWT;
use Lion\Security\RSA;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use ReflectionException;
use Tests\Providers\AuthJwtProviderTrait;

class JWTMiddlewareTest extends Test
{
    use AuthJwtProviderTrait;

    private const string MESSAGE = 'ERR';

    private JWTMiddleware $jWTMiddleware;
    private string $users_code;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->jWTMiddleware = (new JWTMiddleware())
            ->setStore(new Store())
            ->setRSA(new RSA())
            ->setJWT(new JWT());

        $this->users_code = uniqid('code-');

        $this->initReflection($this->jWTMiddleware);
    }

    protected function tearDown(): void
    {
        $this->rmdirRecursively(storage_path(env('RSA_URL_PATH') . "{$this->users_code}/"));

        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setStore(): void
    {
        $this->assertInstanceOf(JWTMiddleware::class, $this->jWTMiddleware->setStore(new Store()));
        $this->assertInstanceOf(Store::class, $this->getPrivateProperty('store'));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setRSA(): void
    {
        $this->assertInstanceOf(JWTMiddleware::class, $this->jWTMiddleware->setRSA(new RSA()));
        $this->assertInstanceOf(RSA::class, $this->getPrivateProperty('rsa'));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setJWT(): void
    {
        $this->assertInstanceOf(JWTMiddleware::class, $this->jWTMiddleware->setJWT(new JWT()));
        $this->assertInstanceOf(JWT::class, $this->getPrivateProperty('jwt'));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function initRSA(): void
    {
        $this->getPrivateMethod('initRSA', [env('RSA_URL_PATH')]);

        /** @var RSA $rsa */
        $rsa = $this->getPrivateProperty('rsa');

        $this->assertSame(storage_path(env('RSA_URL_PATH')), $rsa->getUrlPath());
    }

    /**
     * @throws Exception
     * @throws MiddlewareException
     */
    #[Testing]
    public function validateSessionIsError(): void
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
    #[Testing]
    public function validateSessionNotSession(): void
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
    #[Testing]
    public function existence(): void
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
    #[Testing]
    public function authorizeWithoutSignatureInvalidJWT(): void
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
    #[Testing]
    public function authorizeWithoutSignatureWithoutUsersCode(): void
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
    #[Testing]
    public function authorizeWithoutSignaturePathNotExist(): void
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

                $this->rmdirRecursively(storage_path(env('RSA_URL_PATH') . "{$this->users_code}/"));

                $this->jWTMiddleware->authorizeWithoutSignature();
            });
    }

    /**
     * @throws Exception
     * @throws MiddlewareException
     */
    #[Testing]
    public function authorizeWithoutSignatureNotAuthorize(): void
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
    #[Testing]
    public function authorize(): void
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
    #[Testing]
    public function notAuthorize(): void
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
