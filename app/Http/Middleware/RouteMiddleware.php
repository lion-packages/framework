<?php

declare(strict_types=1);

namespace App\Http\Middleware;

class RouteMiddleware
{
    private array $headers;

    public function __construct()
    {
        $this->headers = apache_request_headers();
    }

    public function protectRouteList(): void
    {
        if (empty($this->headers['Lion-Auth'])) {
            finish(response('session-error', 'Secure hash not found [1]', 401));
        }

        if (empty(env->SERVER_HASH)) {
            finish(response('session-error', 'Secure hash not found [2]', 401));
        }

        if (env->SERVER_HASH != $this->headers['Lion-Auth']) {
            finish(response('session-error', 'You do not have access to this resource', 401));
        }
    }
}
