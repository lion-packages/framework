<?php

declare(strict_types=1);

use App\Http\Controllers\LionDatabase\MySQL\LoginController;
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
    Route::post('auth', [LoginController::class, 'auth']);

    Route::middleware(['jwt-authorize'], function () {
        Route::post('users', [UsersController::class, 'createUsers']);
        Route::get('users', [UsersController::class, 'readUsers']);
        Route::put('users/{idusers}', [UsersController::class, 'updateUsers']);
        Route::delete('users/{idusers}', [UsersController::class, 'deleteUsers']);
    });
});
