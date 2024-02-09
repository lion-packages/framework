<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Lion\Files\Store;
use Lion\Security\RSA;

class JWTMiddleware
{
    private Store $store;
    private RSA $rsa;

    private array $headers;

    public function __construct()
    {
        $this->store = new Store();
        $this->rsa = new RSA();

        $this->headers = apache_request_headers();
    }

    private function validateSession($jwt): void
    {
        if (isError($jwt)) {
            finish(response->code(401)->response('session-error', $jwt->message));
        }

        if (!isset($jwt->data->jwt->data->session)) {
            finish(response->code(403)->response('session-error', 'undefined session'));
        }
    }

    public function existence(): void
    {
        if (!isset($this->headers['Authorization'])) {
            finish(response->code(401)->response('session-error', 'the JWT does not exist'));
        }
    }

    public function authorizeWithoutSignature(): void
    {
        $this->existence();
        $jwt = explode('.', jwt());

        if (arr->of($jwt)->length() != 3) {
            finish(response->code(401)->response('session-error', 'invalid JWT [AWS-1]'));
        }

        $data = (object) ((object) json_decode(base64_decode($jwt[1]), true))->data;

        if (!isset($data->users_code)) {
            finish(response->code(403)->response('session-error', 'invalid JWT [AWS-2]'));
        }

        $path = storage_path("keys/{$data->users_code}/");

        if (isError($this->store->exist($path))) {
            finish(response->code(403)->response('session-error', 'invalid JWT [AWS-3]'));
        }

        $this->rsa->setUrlPath(storage_path($path));
    }

    public function authorize(): void
    {
        $this->existence();
        $jwt = jwt();
        $this->validateSession($jwt);

        if (!$jwt->data->jwt->data->session) {
            finish(response->code(401)->response('session-error', 'user not logged in, you must log in'));
        }
    }

    public function notAuthorize(): void
    {
        $this->existence();
        $jwt = jwt();
        $this->validateSession($jwt);

        if ($jwt->data->jwt->data->session) {
            finish(response->code(401)->response('session-error', 'user in session, you must close the session'));
        }
    }
}
