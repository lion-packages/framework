<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use App\Enums\RolesEnum;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class UsersControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    const array JSON_UPDATE_USERS = [
        'idroles' => 1,
        'iddocument_types' => 1,
        'users_citizen_identification' => '##########',
        'users_name' => 'Sergio',
        'users_last_name' => 'Leon',
        'users_nickname' => 'Sleon',
        'users_email' => 'sleon@dev.com',
    ];

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testCreateUsers(): void
    {
        $encode = $this->AESEncode(['idroles' => (string) RolesEnum::ADMINISTRATOR->value]);

        $response = fetch(Http::POST, env('SERVER_URL') . '/api/users', [
            'headers' => [
                'Authorization' => $this->getAuthorization([
                    'idroles' => $encode['idroles'],
                ]),
            ],
            'json' => [
                'idroles' => RolesEnum::ADMINISTRATOR->value,
                'iddocument_types' => DocumentTypesEnum::PASSPORT->value,
                'users_citizen_identification' => fake()->numerify('##########'),
                'users_name' => fake()->name(),
                'users_last_name' => fake()->lastName(),
                'users_nickname' => fake()->userName(),
                'users_email' => fake()->email(),
                'users_password' => 'cbfad02f9ed2a8d1e08d8f74f5303e9eb93637d47f82ab6f1c15871cf8dd0481',
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'registered user successfully',
        ]);
    }

    public function testReadUsers(): void
    {
        $encode = $this->AESEncode(['idroles' => (string) RolesEnum::ADMINISTRATOR->value]);

        $users = json_decode(
            fetch(Http::GET, env('SERVER_URL') . '/api/users', [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idroles' => $encode['idroles'],
                    ]),
                ],
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

        $encode = $this->AESEncode(['idroles' => (string) RolesEnum::ADMINISTRATOR->value]);

        $users = fetch(Http::GET, env('SERVER_URL') . '/api/users', [
            'headers' => [
                'Authorization' => $this->getAuthorization([
                    'idroles' => $encode['idroles'],
                ]),
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($users, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'no data available'
        ]);
    }

    public function testReadUsersById(): void
    {
        $encode = $this->AESEncode(['idroles' => (string) RolesEnum::ADMINISTRATOR->value]);

        $users = json_decode(
            fetch(Http::GET, env('SERVER_URL') . '/api/users', [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idroles' => $encode['idroles'],
                    ]),
                ],
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
            fetch(Http::GET, (env('SERVER_URL') . '/api/users/' . $firstUser->idusers), [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idroles' => $encode['idroles'],
                    ]),
                ],
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

        $encode = $this->AESEncode(['idroles' => (string) RolesEnum::ADMINISTRATOR->value]);

        $users = fetch(Http::GET, env('SERVER_URL') . '/api/users/1', [
            'headers' => [
                'Authorization' => $this->getAuthorization([
                    'idroles' => $encode['idroles'],
                ]),
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($users, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'no data available',
        ]);
    }

    public function testUpdateUsers(): void
    {
        $encode = $this->AESEncode(['idroles' => (string) RolesEnum::ADMINISTRATOR->value]);

        $token = $this->getAuthorization([
            'idroles' => $encode['idroles'],
        ]);

        $users = json_decode(
            fetch(Http::GET, env('SERVER_URL') . '/api/users', [
                'headers' => [
                    'Authorization' => $token,
                ],
            ])
                ->getBody()
                ->getContents(),
            true
        );

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $firstUser = (object) reset($users);

        $response = fetch(Http::PUT, env('SERVER_URL') . '/api/users/' . $firstUser->idusers, [
            'json' => self::JSON_UPDATE_USERS,
            'headers' => [
                'Authorization' => $token,
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'the registered user has been successfully updated',
        ]);

        $users = json_decode(
            fetch(Http::GET, env('SERVER_URL') . '/api/users', [
                'headers' => [
                    'Authorization' => $token,
                ],
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
        $encode = $this->AESEncode(['idroles' => (string) RolesEnum::ADMINISTRATOR->value]);

        $token = $this->getAuthorization([
            'idroles' => $encode['idroles'],
        ]);

        $users = json_decode(
            fetch(Http::GET, env('SERVER_URL') . '/api/users', [
                'headers' => [
                    'Authorization' => $token,
                ],
            ])
                ->getBody()
                ->getContents(),
            true
        );

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $firstUser = (object) reset($users);

        $response = fetch(Http::DELETE, env('SERVER_URL') . '/api/users/' . $firstUser->idusers, [
            'headers' => [
                'Authorization' => $token,
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'the registered user has been successfully deleted',
        ]);

        $users = json_decode(
            fetch(Http::GET, env('SERVER_URL') . '/api/users', [
                'headers' => [
                    'Authorization' => $token,
                ],
            ])
                ->getBody()
                ->getContents()
        );

        $this->assertIsArray($users);
        $this->assertCount(self::REMAINING_USERS, $users);
    }
}
