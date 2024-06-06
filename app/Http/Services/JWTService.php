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
        return $this->decode($rsaPath)->data;
    }

    /**
     * Gets the token from the headers and extracts its information
     *
     * @return stdClass
     */
    public function getToken(): stdClass
    {
        $splitToken = explode('.', $this->jwt->getJWT());

        return json_decode(base64_decode($splitToken[1]));
    }

    /**
     * Gets the generated token information
     *
     * @param string $rsaPath [Path where RSA public and private keys are
     * defined]
     * @param string|null $jwt [Token to decrypt]
     *
     * @return stdClass
     */
    public function decode(string $rsaPath, ?string $jwt = null): stdClass
    {
        return $this->jwt
            ->config([
                'publicKey' => $this->rsa
                    ->setUrlPath($rsaPath)
                    ->init()
                    ->getPublicKey()
            ])
            ->decode(null === $jwt ? jwt() : $jwt)
            ->get();
    }
}
