<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Route\Route;
use Lion\Test\Test;

class RegistrationControllerTest extends Test
{
    const API_URL = 'http://127.0.0.1:8000/api/auth/register';
    const JSON_AUTH = [
        'users_email' => 'root@dev.com',
        'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464'
    ];

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testRegister(): void
    {
        $response = fetch(Route::POST, self::API_URL, ['json' => self::JSON_AUTH])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);
    }
}
