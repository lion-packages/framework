<?php

declare(strict_types=1);

/**
 * -----------------------------------------------------------------------------
 * Predefined constants
 * -----------------------------------------------------------------------------
 */

/**
 * [Defines a null value]
 *
 * @var null
 */
const NULL_VALUE = null;

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
        return "Hello World";
    }
}
