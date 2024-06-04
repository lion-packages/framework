<?php

declare(strict_types=1);

namespace App\Http\Services;

use Lion\Security\JWT;
use Lion\Security\RSA;
use stdClass;

/**
 * Service to manipulate JWT tokens
 *
 * @property RSA $rsa [RSA class object]
 * @property JWT $jwt [JWT class object]
 *
 * @package App\Http\Services
 */
class JWTService
{
    /**
     * [Object of class RSA]
     *
     * @var RSA $rsa
     */
    private RSA $rsa;

    /**
     * [Object of class JWT]
     *
     * @var JWT $jwt
     */
    private JWT $jwt;

    /**
     * @required
     */
    public function setRSA(RSA $rsa): JWTService
    {
        $this->rsa = $rsa;

        return $this;
    }

    /**
     * @required
     */
    public function setJWT(JWT $jwt): JWTService
    {
        $this->jwt = $jwt;

        return $this;
    }

    /**
     * Get the JWT token data
     *
     * @param string $rsaPath [Path where RSA public and private keys are
     * defined]
     *
     * @return array|object|string
     */
    public function getTokenData(string $rsaPath): array|object|string
    {
        $token = $this->jwt
            ->config([
                'publicKey' => $this->rsa
                    ->setUrlPath($rsaPath)
                    ->init()
                    ->getPublicKey()
            ])
            ->decode(jwt())
            ->get();

        return $token->data;
    }

    public function getToken(): stdClass
    {
        $splitToken = explode('.', $this->jwt->getJWT());

        return json_decode(base64_decode($splitToken[1]));
    }
}
