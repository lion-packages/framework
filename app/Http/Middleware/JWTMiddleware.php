<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Lion\Files\Store;
use Lion\Request\Request;
use Lion\Request\Response;
use Lion\Security\JWT;
use Lion\Security\RSA;

/**
 * Responsible for filtering and validating the JWT sent through an HTTP request
 *
 * @package App\Http\Middleware
 */
class JWTMiddleware
{
    /**
     * [Object of class Store]
     *
     * @var Store $store
     */
    private Store $store;

    /**
     * [Object of class RSA]
     *
     * @var RSA $rsa
     */
    private RSA $rsa;

    /**
     * [Object of class JWT]
     *
     * @var JWT $jwt
     */
    private JWT $jwt;

    /**
     * [List of all available headers]
     *
     * @var array $headers
     */
    private array $headers;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->headers = apache_request_headers();
    }

    /**
     * @required
     * */
    public function setStore(Store $store): void
    {
        $this->store = $store;
    }

    /**
     * @required
     * */
    public function setRSA(RSA $rsa): void
    {
        $this->rsa = $rsa;
    }

    /**
     * @required
     * */
    public function setJWT(JWT $jwt): void
    {
        $this->jwt = $jwt;
    }

    private function initRSA(string $path = 'keys/'): void
    {
        $this->rsa
            ->setUrlPath(storage_path($path))
            ->init();
    }

    /**
     * Validate the session defined in the JWT
     *
     * @param  object $jwt [JWT object]
     *
     * @return void
     */
    private function validateSession(object $jwt): void
    {
        if (isError($jwt)) {
            finish(response(Response::SESSION_ERROR, $jwt->message, Request::HTTP_UNAUTHORIZED));
        }

        if (!isset($jwt->data->session)) {
            finish(response(Response::SESSION_ERROR, 'undefined session', Request::HTTP_FORBIDDEN));
        }
    }

    /**
     * Validate if a JWT exists in the headers
     *
     * @return void
     */
    public function existence(): void
    {
        if (!isset($this->headers['Authorization'])) {
            finish(response(Response::SESSION_ERROR, 'the JWT does not exist', Request::HTTP_UNAUTHORIZED));
        }
    }

    /**
     * Validate a JWT in headers even though the signature is not validated
     *
     * @return void
     */
    public function authorizeWithoutSignature(): void
    {
        $this->existence();

        $splitToken = explode('.', $this->jwt->getJWT());

        if (arr->of($splitToken)->length() != 3) {
            finish(response(Response::SESSION_ERROR, 'invalid JWT [AWS-1]', Request::HTTP_UNAUTHORIZED));
        }

        $data = (object) ((object) json_decode(base64_decode($splitToken[1]), true))->data;

        if (!isset($data->users_code)) {
            finish(response(Response::SESSION_ERROR, 'invalid JWT [AWS-2]', Request::HTTP_FORBIDDEN));
        }

        $path = "keys/{$data->users_code}/";

        if (isError($this->store->exist(storage_path($path)))) {
            finish(response(Response::SESSION_ERROR, 'invalid JWT [AWS-3]', Request::HTTP_FORBIDDEN));
        }

        $this->initRSA($path);

        $token = $this->jwt
            ->config(['publicKey' => $this->rsa->getPublicKey()])
            ->decode($this->jwt->getJWT())
            ->get();

        $this->validateSession($token);

        if (!$token->data->session || !isset($token->data->session)) {
            finish(
                response(Response::SESSION_ERROR, 'user not logged in, you must log in', Request::HTTP_UNAUTHORIZED)
            );
        }
    }

    /**
     * Validate a JWT to check if it is still valid and the session is true
     *
     * @return void
     */
    public function authorize(): void
    {
        $this->initRSA();

        $this->existence();

        $token = $this->jwt
            ->config(['publicKey' => $this->rsa->getPublicKey()])
            ->decode($this->jwt->getJWT())
            ->get();

        $this->validateSession($token);

        if (!$token->data->session || !isset($token->data->session)) {
            finish(
                response(Response::SESSION_ERROR, 'user not logged in, you must log in', Request::HTTP_UNAUTHORIZED)
            );
        }
    }

    /**
     * Validate a JWT to check if it is still valid and the session is false
     *
     * @return void
     */
    public function notAuthorize(): void
    {
        $this->initRSA();

        $this->existence();

        $token = $this->jwt
            ->config(['publicKey' => $this->rsa->getPublicKey()])
            ->decode($this->jwt->getJWT())
            ->get();

        $this->validateSession($token);

        if ($token->data->session) {
            finish(
                response(
                    Response::SESSION_ERROR,
                    'user in session, you must close the session',
                    Request::HTTP_UNAUTHORIZED
                )
            );
        }
    }
}
