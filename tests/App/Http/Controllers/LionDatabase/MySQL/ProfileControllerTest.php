<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Route\Route;
use Lion\Test\Test;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class ProfileControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    const IDUSERS = 1;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testReadProfile(): void
    {
        $response = json_decode(
            fetch(Route::GET, (env('SERVER_URL') . '/api/profile'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization(['idusers' => self::IDUSERS])
                ]
            ])
                ->getBody()
                ->getContents()
        );

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('idusers', $response);
        $this->assertObjectHasProperty('idroles', $response);
        $this->assertObjectHasProperty('iddocument_types', $response);
        $this->assertObjectHasProperty('users_citizen_identification', $response);
        $this->assertObjectHasProperty('users_name', $response);
        $this->assertObjectHasProperty('users_last_name', $response);
        $this->assertObjectHasProperty('users_nickname', $response);
        $this->assertObjectHasProperty('users_email', $response);
    }

    public function testUpdateProfile(): void
    {
        $response = fetch(Route::PUT, (env('SERVER_URL') . '/api/profile'), [
            'headers' => [
                'Authorization' => $this->getAuthorization(['idusers' => self::IDUSERS])
            ],
            'json' => [
                'iddocument_types' => DocumentTypesEnum::PASSPORT->value,
                'users_citizen_identification' => fake()->numerify('##########'),
                'users_name' => fake()->name(),
                'users_last_name' => fake()->lastName(),
                'users_nickname' => fake()->userName(),
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::HTTP_OK,
            'status' => Status::SUCCESS,
            'message' => 'profile updated successfully',
        ]);
    }
}
