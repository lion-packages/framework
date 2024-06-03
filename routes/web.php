<?php

declare(strict_types=1);

use App\Http\Controllers\LionDatabase\MySQL\LoginController;
use App\Http\Controllers\LionDatabase\MySQL\PasswordManagerController;
use App\Http\Controllers\LionDatabase\MySQL\ProfileController;
use App\Http\Controllers\LionDatabase\MySQL\RegistrationController;
use App\Http\Controllers\LionDatabase\MySQL\UsersController;
use Lion\Route\Route;

/**
 * -----------------------------------------------------------------------------
 * Web Routes
 * -----------------------------------------------------------------------------
 * Here is where you can register web routes for your application
 * -----------------------------------------------------------------------------
 */

Route::get('/', fn (): stdClass => info('[index]'));

Route::prefix('api', function (): void {
    Route::prefix('auth', function (): void {
        Route::post('login', [LoginController::class, 'auth']);
        Route::post('register', [RegistrationController::class, 'register']);
        Route::post('verify', [RegistrationController::class, 'verifyAccount']);

        Route::prefix('recovery', function (): void {
            Route::post('password', [PasswordManagerController::class, 'recoveryPassword']);
            Route::post('verify-code', [PasswordManagerController::class, 'updateLostPassword']);
        });
    });

    Route::middleware(['jwt-authorize'], function (): void {
        Route::prefix('profile', function (): void {
            Route::get('/', [ProfileController::class, 'readProfile']);
            Route::put('/', [ProfileController::class, 'updateProfile']);
            Route::post('password', [PasswordManagerController::class, 'updatePassword']);
        });

        Route::middleware(['admin-access', 'prefix' => 'users'], function (): void {
            Route::post('/', [UsersController::class, 'createUsers']);
            Route::get('/', [UsersController::class, 'readUsers']);
            Route::get('{idusers:i}', [UsersController::class, 'readUsersById']);
            Route::put('{idusers:i}', [UsersController::class, 'updateUsers']);
            Route::delete('{idusers:i}', [UsersController::class, 'deleteUsers']);
        });
    });
});
