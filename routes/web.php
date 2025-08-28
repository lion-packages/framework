<?php

/**
 * -----------------------------------------------------------------------------
 * Web Routes.
 * -----------------------------------------------------------------------------
 * Here is where you can register web routes for your application.
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

use Lion\Route\Route;

Route::get('/', fn (): stdClass => info('[index]'));
