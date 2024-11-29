<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Database\Migrations\LionDatabase\MySQL\Tables\DocumentTypes as DocumentTypesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Roles as RolesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Users as UsersTable;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsersById;
use Database\Seed\LionDatabase\MySQL\DocumentTypesSeed;
use Database\Seed\LionDatabase\MySQL\RolesSeed;
use Database\Seed\LionDatabase\MySQL\UsersSeed;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Lion\Bundle\Test\Test;
use Lion\Database\Drivers\MySQL as DB;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use PHPUnit\Framework\Attributes\Test as Testing;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;

class RegistrationControllerTest extends Test
{
    use AuthJwtProviderTrait;

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

        Schema::truncateTable('users')->execute();
    }

    /**
     * @throws GuzzleException
     */
    #[Testing]
    public function register(): void
    {
        $encode = $this->AESEncode(['users_password' => UsersFactory::USERS_PASSWORD]);

        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/register'), [
            'json' => [
                'users_email' => UsersFactory::USERS_EMAIL,
                'users_password' => $encode['users_password'],
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user successfully registered, check your mailbox to obtain the account activation code',
        ]);
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    #[Testing]
    public function registerRegistered(): void
    {
        $encode = $this->AESEncode(['users_password' => UsersFactory::USERS_PASSWORD]);

        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/register'), [
            'json' => [
                'users_email' => UsersFactory::USERS_EMAIL,
                'users_password' => $encode['users_password'],
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user successfully registered, check your mailbox to obtain the account activation code',
        ]);

        $exception = $this->getExceptionFromApi(function () use ($encode): void {
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/register'), [
                'json' => [
                    'users_email' => UsersFactory::USERS_EMAIL,
                    'users_password' => $encode['users_password'],
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::BAD_REQUEST,
            'status' => Status::ERROR,
            'message' => 'there is already an account registered with this email',
        ]);
    }

    /**
     * @throws GuzzleException
     */
    #[Testing]
    public function verifyAccount(): void
    {
        $encode = $this->AESEncode(['users_password' => UsersFactory::USERS_PASSWORD]);

        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/register'), [
            'json' => [
                'users_email' => UsersFactory::USERS_EMAIL,
                'users_password' => $encode['users_password'],
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user successfully registered, check your mailbox to obtain the account activation code',
        ]);

        /** @var stdClass $users_activation_code */
        $users_activation_code = DB::table('users')
            ->select('users_activation_code')
            ->where()->equalTo('users_email', UsersFactory::USERS_EMAIL)
            ->get();

        $this->assertIsObject($users_activation_code);
        $this->assertInstanceOf(stdClass::class, $users_activation_code);
        $this->assertIsString($users_activation_code->users_activation_code);

        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/verify'), [
            'json' => [
                'users_email' => UsersFactory::USERS_EMAIL,
                'users_activation_code' => $users_activation_code->users_activation_code
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user account has been successfully verified'
        ]);
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    #[Testing]
    public function verifyAccountInvalid1(): void
    {
        $encode = $this->AESEncode(['users_password' => UsersFactory::USERS_PASSWORD]);

        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/register'), [
            'json' => [
                'users_email' => UsersFactory::USERS_EMAIL,
                'users_password' => $encode['users_password'],
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user successfully registered, check your mailbox to obtain the account activation code',
        ]);

        $exception = $this->getExceptionFromApi(function (): void {
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/verify'), [
                'json' => [
                    'users_activation_code' => fake()->numerify('######'),
                    'users_email' => fake()->email()
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::FORBIDDEN,
            'status' => Status::SESSION_ERROR,
            'message' => 'verification code is invalid [ERR-1]',
        ]);
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    #[Testing]
    public function verifyAccountInvalid2(): void
    {
        $encode = $this->AESEncode(['users_password' => UsersFactory::USERS_PASSWORD]);

        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/register'), [
            'json' => [
                'users_email' => UsersFactory::USERS_EMAIL,
                'users_password' => $encode['users_password'],
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user successfully registered, check your mailbox to obtain the account activation code',
        ]);

        $exception = $this->getExceptionFromApi(function (): void {
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/verify'), [
                'json' => [
                    'users_email' => UsersFactory::USERS_EMAIL,
                    'users_activation_code' => fake()->numerify('######'),
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::FORBIDDEN,
            'status' => Status::SESSION_ERROR,
            'message' => 'verification code is invalid [ERR-2]',
        ]);
    }
}
