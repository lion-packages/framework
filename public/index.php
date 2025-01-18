<?php

define('LION_START', microtime(true));

const IS_INDEX = true;

/**
 * -----------------------------------------------------------------------------
 * Register The Auto Loader
 * -----------------------------------------------------------------------------
 * Composer provides a convenient, automatically generated class loader for this
 * application
 * -----------------------------------------------------------------------------
 */

require_once(__DIR__ . '/../vendor/autoload.php');

/**
 * -----------------------------------------------------------------------------
 * Initialization file
 * -----------------------------------------------------------------------------
 * Class initialization file, initialize all the resources needed for your web
 * application
 * -----------------------------------------------------------------------------
 */

include_once(__DIR__ . '/../config/bootstrap.php');
