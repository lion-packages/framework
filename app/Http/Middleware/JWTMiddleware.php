<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Lion\Bundle\Enums\StatusResponseEnum;
use Lion\Files\Store;
use Lion\Security\RSA;

class JWTMiddleware
{
    private Store $store;
    private RSA $rsa;

    private array $headers;

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
    public function setStore(Store $store): void
    {
        $this->store = $store;
    }

    private function validateSession($jwt): void
    {
        if (isError($jwt)) {
            finish(response(StatusResponseEnum::SESSION_ERROR->value, $jwt->message, 401));
        }

        if (!isset($jwt->data->jwt->data->session)) {
            finish(response(StatusResponseEnum::SESSION_ERROR->value, 'undefined session', 403));
        }
    }

    public function existence(): void
    {
        if (!isset($this->headers['Authorization'])) {
            finish(response(StatusResponseEnum::SESSION_ERROR->value, 'the JWT does not exist', 401));
        }
    }

    public function authorizeWithoutSignature(): void
    {
        $this->existence();
        $jwt = explode('.', jwt());

        if (arr->of($jwt)->length() != 3) {
            finish(response(StatusResponseEnum::SESSION_ERROR->value, 'invalid JWT [AWS-1]', 401));
        }

        $data = (object) ((object) json_decode(base64_decode($jwt[1]), true))->data;

        if (!isset($data->users_code)) {
            finish(response(StatusResponseEnum::SESSION_ERROR->value, 'invalid JWT [AWS-2]', 403));
        }

        $path = storage_path("keys/{$data->users_code}/");

        if (isError($this->store->exist($path))) {
            finish(response(StatusResponseEnum::SESSION_ERROR->value, 'invalid JWT [AWS-3]', 403));
        }

        $this->rsa->setUrlPath(storage_path($path));
    }

    public function authorize(): void
    {
        $this->existence();
        $jwt = jwt();
        $this->validateSession($jwt);

        if (!$jwt->data->jwt->data->session) {
            finish(response(StatusResponseEnum::SESSION_ERROR->value, 'user not logged in, you must log in', 401));
        }
    }

    public function notAuthorize(): void
    {
        $this->existence();
        $jwt = jwt();
        $this->validateSession($jwt);

        if ($jwt->data->jwt->data->session) {
            finish(response(StatusResponseEnum::SESSION_ERROR->value, 'user in session, you must close the session', 401));
        }
    }
}
