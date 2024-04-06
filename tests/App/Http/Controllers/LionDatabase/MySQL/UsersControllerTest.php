<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use App\Enums\RolesEnum;
use Lion\Command\Kernel;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Route\Route;
use Lion\Test\Test;
use Tests\Providers\AuthJwtProviderTrait;

class UsersControllerTest extends Test
{
    use AuthJwtProviderTrait;

    const API_URL = 'http://127.0.0.1:8000/api/users';
    const JSON_UPDATE_USERS = [
        'idroles' => 1,
        'iddocument_types' => 1,
        'users_citizen_identification' => '##########',
        'users_name' => 'Sergio D',
        'users_last_name' => 'Leon G',
        'users_email' => 'sleon@dev.com'
    ];

    protected function setUp(): void
    {
        (new Kernel())->execute('php lion migrate:fresh --seed', false);
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testCreateUsers(): void
    {
        $response = fetch(Route::POST, self::API_URL, [
            'headers' => [
                'Authorization' => $this->getAuthorization()
            ],
            'json' => [
                'idroles' => RolesEnum::ADMINISTRATOR->value,
                'iddocument_types' => DocumentTypesEnum::PASSPORT->value,
                'users_citizen_identification' => fake()->numerify('##########'),
                'users_name' => fake()->name(),
                'users_last_name' => fake()->lastName(),
                'users_email' => 'sleon@dev.com',
                'users_password' => 'cbfad02f9ed2a8d1e08d8f74f5303e9eb93637d47f82ab6f1c15871cf8dd0481'
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);
    }

    public function testReadUsers(): void
    {
        $users = json_decode(
            fetch(Route::GET, self::API_URL, [
                'headers' => [
                    'Authorization' => $this->getAuthorization()
                ]
            ])
                ->getBody()
                ->getContents(),
            true
        );

        $this->assertFalse(isSuccess($users));
        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);
    }

    public function testReadUsersWithoutData(): void
    {
        Schema::truncateTable('users')->execute();

        $users = fetch(Route::GET, self::API_URL, [
            'headers' => [
                'Authorization' => $this->getAuthorization()
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($users, [
            'status' => 'success',
            'message' => 'no data available'
        ]);
    }

    public function testReadUsersById(): void
    {
        $users = json_decode(
            fetch(Route::GET, self::API_URL, [
                'headers' => [
                    'Authorization' => $this->getAuthorization()
                ]
            ])
                ->getBody()
                ->getContents(),
            true
        );

        $this->assertFalse(isSuccess($users));
        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $firstUser = (object) reset($users);

        $user = json_decode(
            fetch(Route::GET, (self::API_URL . '/' . $firstUser->idusers), [
                'headers' => [
                    'Authorization' => $this->getAuthorization()
                ]
            ])
                ->getBody()
                ->getContents(),
            true
        );

        $this->assertFalse(isSuccess($user));
        $this->assertIsArray($user);
        $this->assertSame('root', $user['users_name']);
        $this->assertSame('lion', $user['users_last_name']);
    }

    public function testReadUsersByIdWithoutData(): void
    {
        Schema::truncateTable('users')->execute();

        $users = fetch(Route::GET, self::API_URL . '/1', [
            'headers' => [
                'Authorization' => $this->getAuthorization()
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($users, [
            'status' => 'success',
            'message' => 'no data available'
        ]);
    }

    public function testUpdateUsers(): void
    {
        $token = $this->getAuthorization();

        $users = json_decode(
            fetch(Route::GET, self::API_URL, [
                'headers' => [
                    'Authorization' => $token
                ]
            ])
                ->getBody()
                ->getContents(),
            true
        );

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $firstUser = (object) reset($users);

        $response = fetch(Route::PUT, self::API_URL . '/' . $firstUser->idusers, [
            'json' => self::JSON_UPDATE_USERS,
            'headers' => [
                'Authorization' => $token
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);

        $users = json_decode(
            fetch(Route::GET, self::API_URL, [
                'headers' => [
                    'Authorization' => $token
                ]
            ])
                ->getBody()
                ->getContents(),
            true
        );

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $firstUser = (object) reset($users);

        $this->assertSame(self::JSON_UPDATE_USERS['users_name'], $firstUser->users_name);
        $this->assertSame(self::JSON_UPDATE_USERS['users_last_name'], $firstUser->users_last_name);
    }

    public function testDeleteUsers(): void
    {
        $token = $this->getAuthorization();

        $users = json_decode(
            fetch(Route::GET, self::API_URL, [
                'headers' => [
                    'Authorization' => $token
                ]
            ])
                ->getBody()
                ->getContents(),
            true
        );

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $firstUser = (object) reset($users);

        $response = fetch(Route::DELETE, self::API_URL . '/' . $firstUser->idusers, [
            'headers' => [
                'Authorization' => $token
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);

        $users = json_decode(
            fetch(Route::GET, self::API_URL, [
                'headers' => [
                    'Authorization' => $token
                ]
            ])
                ->getBody()
                ->getContents()
        );

        $this->assertIsArray($users);
        $this->assertCount(self::REMAINING_USERS, $users);
    }
}
