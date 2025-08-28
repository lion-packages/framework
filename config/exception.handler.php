<?php

/**
 * -----------------------------------------------------------------------------
 * Initialize exception handling.
 * -----------------------------------------------------------------------------
 * Controls and serializes exceptions to JSON format.
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

use Lion\Bundle\Enums\LogTypeEnum;
use Lion\Bundle\Support\ExceptionHandler;

ExceptionHandler::handle([

    /**
     * -------------------------------------------------------------------------
     * Add additional exception information to the JSON.
     * -------------------------------------------------------------------------
     * If true, the exception information will be added to the JSON response.
     * -------------------------------------------------------------------------
     */

    'addInformation' => false,

    /**
     * -------------------------------------------------------------------------
     * Callback function to handle exceptions.
     * -------------------------------------------------------------------------
     * This function will be called when an exception occurs.
     * -------------------------------------------------------------------------
     */

    'callback' => function (int|string $code, Throwable $exception): void {
        logger($exception->getMessage(), LogTypeEnum::ERROR, [
            'exception-type' => get_class($exception),
            'exception-code' => $code,
            'exception-message' => $exception->getMessage(),
            'exception-file' => $exception->getFile(),
            'exception-line' => $exception->getLine(),
            'exception-trace' => $exception->getTraceAsString(),
            'exception-previous' => $exception->getPrevious(),
        ]);
    },

]);
