<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use GuzzleHttp\Exception\GuzzleException;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class ProfileControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private const int IDUSERS = 1;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    /**
     * @throws GuzzleException
     */
    #[Testing]
    public function readProfile(): void
    {
        $encode = $this->AESEncode(['idusers' => (string) self::IDUSERS]);

        $response = json_decode(
            fetch(Http::GET, (env('SERVER_URL') . '/api/profile'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idusers' => $encode['idusers'],
                    ])
                ]
            ])
                ->getBody()
                ->getContents()
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('idusers', $response);
        $this->assertObjectHasProperty('idroles', $response);
        $this->assertObjectHasProperty('iddocument_types', $response);
        $this->assertObjectHasProperty('users_citizen_identification', $response);
        $this->assertObjectHasProperty('users_name', $response);
        $this->assertObjectHasProperty('users_last_name', $response);
        $this->assertObjectHasProperty('users_nickname', $response);
        $this->assertObjectHasProperty('users_email', $response);
        $this->assertIsInt($response->idusers);
        $this->assertIsInt($response->idroles);
        $this->assertIsInt($response->iddocument_types);
        $this->assertIsString($response->users_citizen_identification);
        $this->assertIsString($response->users_name);
        $this->assertIsString($response->users_last_name);
        $this->assertIsString($response->users_nickname);
        $this->assertIsString($response->users_email);
    }

    /**
     * @throws GuzzleException
     */
    #[Testing]
    public function updateProfile(): void
    {
        $encode = $this->AESEncode(['idusers' => (string) self::IDUSERS]);

        $response = fetch(Http::PUT, (env('SERVER_URL') . '/api/profile'), [
            'headers' => [
                'Authorization' => $this->getAuthorization([
                    'idusers' => $encode['idusers'],
                ]),
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
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'profile updated successfully',
        ]);
    }
}
