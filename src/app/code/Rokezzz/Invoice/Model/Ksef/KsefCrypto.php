<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;
use Rokezzz\Invoices\Exception\KsefApiException;

final class KsefCrypto
{
    public function encryptRsaOaepSha256Base64(string $data, string $publicKeyPem): string
    {
        $publicKey = PublicKeyLoader::load($publicKeyPem)
            ->withPadding(RSA::ENCRYPTION_OAEP)
            ->withHash('sha256')
            ->withMGFHash('sha256');

        return base64_encode($publicKey->encrypt($data));
    }

    /**
     * @return array{encryptedInvoiceHash: string, encryptedInvoiceSize: int, encryptedInvoiceContent: string}
     */
    public function encryptInvoicePayload(string $xmlContent, string $aesKey, string $aesIv): array
    {
        $encryptedBytes = openssl_encrypt(
            $xmlContent,
            'AES-256-CBC',
            $aesKey,
            OPENSSL_RAW_DATA,
            $aesIv
        );

        if ($encryptedBytes === false) {
            throw new KsefApiException('OpenSSL AES-256-CBC encryption failed.');
        }

        return [
            'encryptedInvoiceHash' => base64_encode(hash('sha256', $encryptedBytes, true)),
            'encryptedInvoiceSize' => strlen($encryptedBytes),
            'encryptedInvoiceContent' => base64_encode($encryptedBytes),
        ];
    }

    public function certificateBase64ToPem(string $certificateBase64): string
    {
        return "-----BEGIN CERTIFICATE-----\n"
            . chunk_split($certificateBase64, 64, "\n")
            . "-----END CERTIFICATE-----\n";
    }
}
