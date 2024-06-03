<?php

declare(strict_types=1);

namespace App\Http\Services;

use Lion\Security\AES;

/**
 * Encrypt and decrypt data with AES
 *
 * @property AES $aes [It allows you to generate the configuration required for
 * AES encryption and decryption, it has methods that allow you to encrypt and
 * decrypt data with AES]
 *
 * @package App\Http\Services
 */
class AESService
{
    /**
     * [It allows you to generate the configuration required for AES encryption
     * and decryption, it has methods that allow you to encrypt and decrypt data
     * with AES]
     *
     * @var AES $aes
     */
    private AES $aes;

    /**
     * @required
     */
    public function setAES(AES $aes): AESService
    {
        $this->aes = $aes;

        return $this;
    }

    /**
     * Encrypt the data list with AES
     *
     * @param array<string, string> $rows [List of data to encrypt]
     *
     * @return array|object
     */
    public function encode(array $rows): array|object
    {
        $this->aes->config([
            'passphrase' => env('AES_PASSPHRASE'),
            'iv' => env('AES_IV'),
            'method' => env('AES_METHOD'),
        ]);

        foreach ($rows as $key => $value) {
            $this->aes->encode($key, $value);
        }

        return $this->aes->get();
    }

    /**
     * Decrypt data list with AES
     *
     * @param array<string, string> $rows [List of data to decrypt]
     *
     * @return array|object
     */
    public function decode(array $rows): array|object
    {
        return $this->aes
            ->config([
                'passphrase' => env('AES_PASSPHRASE'),
                'iv' => env('AES_IV'),
                'method' => env('AES_METHOD'),
            ])
            ->decode($rows)
            ->get();
    }
}
