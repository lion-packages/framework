<?php

/**
 * -----------------------------------------------------------------------------
 * Helpers
 * -----------------------------------------------------------------------------
 * Declare your helpers for your web application
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

if (!function_exists('helloWorld')) {
    function helloWorld(): string
    {
        return 'Hello World';
    }
}
