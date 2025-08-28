<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Lion\Bundle\Support\Http\Fetch;
use Lion\Bundle\Test\Test;
use Lion\Request\Http;
use Lion\Request\Status;
use PHPUnit\Framework\Attributes\Test as Testing;

class AppTest extends Test
{
    /**
     * @throws GuzzleException If the request fails.
     * @throws JsonException If encoding to JSON fails.
     */
    #[Testing]
    public function api(): void
    {
        /** @var string $url */
        $url = env('SERVER_URL');

        $response = fetch(new Fetch(Http::GET, $url))
            ->getBody()
            ->getContents();

        $this->assertJsonStringEqualsJsonString(json([
            CODE => Http::OK,
            STATUS => Status::INFO,
            MESSAGE => '[index]',
        ]), $response);
    }
}
