<?php

declare(strict_types=1);

namespace App\Http\Services;

use Lion\Security\JWT;
use Lion\Security\RSA;

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
    public function setRSA(RSA $rsa): void
    {
        $this->rsa = $rsa;
    }

    /**
     * @required
     */
    public function setJWT(JWT $jwt): void
    {
        $this->jwt = $jwt;
    }

    /**
     * Get the JWT token data
     *
     * @param string $rsaPath [Path where RSA public and private keys are
     * defined]
     *
     * @return object
     */
    public function getTokenData(string $rsaPath): object
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
}
