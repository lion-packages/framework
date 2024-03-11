<?php

declare(strict_types=1);

namespace Tests\Providers;

trait ResponseProviderTrait
{
    private function getResponse(string $message, string $messageSplit = 'response:'): string
    {
        $split = explode($messageSplit, $message);

        return trim(end($split));
    }
}
