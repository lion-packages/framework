<?php

declare(strict_types=1);

namespace Tests\Providers;

use Lion\Command\Kernel;
use Lion\Route\Route;

trait AuthJwtProviderTrait
{
    const AVAILABLE_USERS = 2;
    const REMAINING_USERS = 1;

    private function getJWT(): string
    {
        (new Kernel)->execute('php lion migrate:fresh --seed', false);

        $auth = json_decode(
            fetch(Route::POST, 'http://127.0.0.1:8000/api/auth/login', [
                'json' => [
                    'users_email' => 'root@dev.com',
                    'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464'
                ]
            ])
                ->getBody()
                ->getContents()
        );

        return $auth->data->jwt;
    }
}
