<?php

declare(strict_types=1);

use App\Http\Controllers\LionDatabase\MySQL\LoginController;
use App\Http\Controllers\LionDatabase\MySQL\PasswordManagerController;
use App\Http\Controllers\LionDatabase\MySQL\RegistrationController;
use App\Http\Controllers\LionDatabase\MySQL\UsersController;
use Lion\Route\Route;

/**
 * -----------------------------------------------------------------------------
 * Web Routes
 * -----------------------------------------------------------------------------
 * Here is where you can register web routes for your application
 * -----------------------------------------------------------------------------
 **/

Route::get('/', fn () => info('[index]'));

Route::prefix('api', function () {
    Route::prefix('auth', function () {
        Route::post('login', [LoginController::class, 'auth']);
        Route::post('register', [RegistrationController::class, 'register']);
        Route::post('verify', [RegistrationController::class, 'verifyAccount']);

        Route::prefix('password', function () {
            Route::post('update', [PasswordManagerController::class, 'updatePassword'], ['jwt-authorize']);
        });
    });

    Route::middleware(['jwt-authorize'], function () {
        Route::post('users', [UsersController::class, 'createUsers']);
        Route::get('users', [UsersController::class, 'readUsers']);
        Route::get('users/{idusers:i}', [UsersController::class, 'readUsersById']);
        Route::put('users/{idusers:i}', [UsersController::class, 'updateUsers']);
        Route::delete('users/{idusers:i}', [UsersController::class, 'deleteUsers']);
    });
});
