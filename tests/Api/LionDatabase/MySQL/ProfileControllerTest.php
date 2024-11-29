<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use Database\Migrations\LionDatabase\MySQL\Tables\DocumentTypes as DocumentTypesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Roles as RolesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Users as UsersTable;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsersById;
use Database\Seed\LionDatabase\MySQL\DocumentTypesSeed;
use Database\Seed\LionDatabase\MySQL\RolesSeed;
use Database\Seed\LionDatabase\MySQL\UsersSeed;
use GuzzleHttp\Exception\GuzzleException;
use Lion\Bundle\Test\Test;
use Lion\Request\Http;
use Lion\Request\Status;
use PHPUnit\Framework\Attributes\Test as Testing;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;

class ProfileControllerTest extends Test
{
    use AuthJwtProviderTrait;

    private const int IDUSERS = 1;

    protected function setUp(): void
    {
        $this->executeMigrationsGroup([
            DocumentTypesTable::class,
            RolesTable::class,
            UsersTable::class,
            ReadUsersById::class,
        ]);

        $this->executeSeedsGroup([
            DocumentTypesSeed::class,
            RolesSeed::class,
            UsersSeed::class,
        ]);
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
