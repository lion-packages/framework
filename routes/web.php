<?php

/**
 * -----------------------------------------------------------------------------
 * Web Routes
 * -----------------------------------------------------------------------------
 * Here is where you can register web routes for your application
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

use Lion\Database\Drivers\MySQL;
use Lion\Route\Route;

Route::get('/', fn (): stdClass => info('[index]'));

Route::get('users', function (): array {
    return MySQL::connection(env('DB_DEFAULT', 'local'))
        ->table('users')
        ->select()
        ->getAll();
});

Route::get('users/{idusers}', function (string $idusers): stdClass {
    return MySQL::connection(env('DB_DEFAULT', 'local'))
        ->table('users')
        ->select()
        ->where()->equalTo('idusers', (int) $idusers)
        ->get();
});

Route::post('users', function (): stdClass {
    /** @var string $usersName */
    $usersName = request('users_name');

    if (empty($usersName)) {
        return error('The field "users_name" is required');
    }

    return MySQL::connection(env('DB_DEFAULT', 'local'))
        ->table('users')
        ->insert([
            'users_name' => $usersName,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ])
        ->execute();
});
