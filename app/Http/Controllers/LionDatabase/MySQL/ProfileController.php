<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Services\JWTService;
use App\Models\LionDatabase\MySQL\ProfileModel;
use Database\Class\LionDatabase\MySQL\Users;

/**
 * Description of Controller 'ProfileController'
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
     *
     * @return array|object
     */
    public function readProfile(Users $users, ProfileModel $profileModel, JWTService $jWTService): array|object
    {
        $data = $jWTService->getTokenData(storage_path(env('RSA_URL_PATH')));

        return $profileModel->readProfileDB(
            $users
                ->setIdusers($data->idusers)
        );
    }

    /**
     * Description of 'updateProfile'
     *
     * @param ProfileModel $profileModel [Parameter Description]
     * @param string $id [Parameter Description]
     *
     * @return object
     */
    public function updateProfile(ProfileModel $profileModel, string $id): object
    {
        return $profileModel->updateProfileDB();
    }

    /**
     * Description of 'deleteProfile'
     *
     * @param ProfileModel $profileModel [Parameter Description]
     * @param string $id [Parameter Description]
     *
     * @return object
     */
    public function deleteProfile(ProfileModel $profileModel, string $id): object
    {
        return $profileModel->deleteProfileDB();
    }
}
