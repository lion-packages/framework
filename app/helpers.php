<?php

declare(strict_types=1);

/**
 * -----------------------------------------------------------------------------
 * Helpers
 * -----------------------------------------------------------------------------
 * Declare your helpers for your web application
 * -----------------------------------------------------------------------------
 */

if (!function_exists('helloWorld')) {
    function helloWorld(): string
    {
        return 'Hello World';
    }
}
