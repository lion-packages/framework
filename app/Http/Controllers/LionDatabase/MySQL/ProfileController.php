<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use App\Models\LionDatabase\MySQL\ProfileModel;
use Database\Class\LionDatabase\MySQL\Users;
use Exception;
use Lion\Request\Http;

/**
 * Manage user profile
 *
 * @package App\Http\Controllers\LionDatabase\MySQL
 */
class ProfileController
{
    /**
     * Get profile data
     *
     * @route /api/profile
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param ProfileModel $profileModel [Model for user profile data]
     * @param JWTService $jWTService [Service to manipulate JWT tokens]
     * @param AESService $aESService [Encrypt and decrypt data with AES]
     *
     * @return array|object
     */
    public function readProfile(
        Users $users,
        ProfileModel $profileModel,
        JWTService $jWTService,
        AESService $aESService
    ): array|object {
        $data = $jWTService->getTokenData(env('RSA_URL_PATH'));

        $decode = $aESService->decode(['idusers' => $data->idusers]);

        return $profileModel->readProfileDB(
            $users
                ->setIdusers((int) $decode['idusers'])
        );
    }

    /**
     * Update the user's personal information
     *
     * @route /api/profile
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param ProfileModel $profileModel [Parameter Description]
     * @param JWTService $jWTService [Service to manipulate JWT tokens]
     * @param AESService $aESService [Encrypt and decrypt data with AES]
     *
     * @return object
     *
     * @throws Exception
     */
    public function updateProfile(
        Users $users,
        ProfileModel $profileModel,
        JWTService $jWTService,
        AESService $aESService
    ): object {
        $data = $jWTService->getTokenData(env('RSA_URL_PATH'));

        $decode = $aESService->decode(['idusers' => $data->idusers]);

        $response = $profileModel->updateProfileDB(
            $users
                ->capsule()
                ->setIdusers((int) $decode['idusers'])
        );

        if (isError($response)) {
            throw new Exception(
                "an error occurred while updating the user's profile",
                Http::INTERNAL_SERVER_ERROR
            );
        }

        return success('profile updated successfully');
    }
}
