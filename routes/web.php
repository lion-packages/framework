<?php

declare(strict_types=1);

use App\Http\Controllers\LionDatabase\MySQL\AuthenticatorController;
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

Route::get('/', fn(): stdClass => info('[index]'));

Route::prefix('api', function (): void {
    Route::prefix('auth', function (): void {
        Route::post('login', [LoginController::class, 'auth']);
        Route::post('register', [RegistrationController::class, 'register']);
        Route::post('verify', [RegistrationController::class, 'verifyAccount']);
        Route::post('refresh', [LoginController::class, 'refresh'], ['jwt-existence']);

        Route::prefix('recovery', function (): void {
            Route::post('password', [PasswordManagerController::class, 'recoveryPassword']);
            Route::post('verify-code', [PasswordManagerController::class, 'updateLostPassword']);
        });
    });

    Route::middleware(['jwt-authorize'], function (): void {
        Route::prefix('profile', function (): void {
            Route::get('/', [ProfileController::class, 'readProfile']);
            Route::put('/', [ProfileController::class, 'updateProfile']);

            Route::prefix('password', function (): void {
                Route::post('/', [PasswordManagerController::class, 'updatePassword']);
                Route::post('verify', [AuthenticatorController::class, 'passwordVerify']);
            });
        });

        Route::middleware(['admin-access'], function (): void {
            Route::controller(UsersController::class, function (): void {
                Route::post('users', 'createUsers');
                Route::get('users', 'readUsers');
                Route::get('users/{idusers:i}', 'readUsersById');
                Route::put('users/{idusers:i}', 'updateUsers');
                Route::delete('users/{idusers:i}', 'deleteUsers');
            });
        });
    });
});
