<?php

declare(strict_types=1);

namespace App\Http;

class HttpKernel
{
    public function checkUrl(string $uri): bool
    {
        $cleanRequestUri = explode('?', $_SERVER['REQUEST_URI'])[0];
        $arrayUri = explode('/', $uri);
        $arrayUrl = explode('/', $cleanRequestUri);

        foreach ($arrayUri as $index => &$value) {
            if (preg_match('/^\{.*\}$/', $value)) {
                $value = 'dynamic-param';
                $arrayUrl[$index] = 'dynamic-param';
            }
        }

        return implode('/', $arrayUri) === implode('/', $arrayUrl);
    }
}
