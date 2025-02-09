<?php

declare(strict_types=1);

use Lion\Route\Route;

/**
 * -----------------------------------------------------------------------------
 * Web Routes
 * -----------------------------------------------------------------------------
 * Here is where you can register web routes for your application
 * -----------------------------------------------------------------------------
 */

Route::get('/', fn (): stdClass => info('[index]'));
