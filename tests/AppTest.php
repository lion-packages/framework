<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Lion\Bundle\Helpers\Http\Fetch;
use Lion\Bundle\Test\Test;
use Lion\Request\Http;
use Lion\Request\Status;
use PHPUnit\Framework\Attributes\Test as Testing;

class AppTest extends Test
{
    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    #[Testing]
    public function api(): void
    {
        /** @var string $url */
        $url = env('SERVER_URL');

        $response = fetch(new Fetch(Http::GET, $url))
            ->getBody()
            ->getContents();

        $this->assertJson($response, json([
            'code' => Http::OK,
            'status' => Status::INFO,
            'message' => '[index]',
        ]));
    }
}
