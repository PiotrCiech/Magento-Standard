<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use Rokezzz\Invoices\Exception\KsefApiException;

class KsefSessionManager
{
    public function __construct(
        private readonly KsefHttpClient $httpClient,
        private readonly KsefCrypto $crypto
    ) {
    }

    public function create(KsefState $state, ?int $storeId = null): string
    {
        $certificatePem = $this->resolveSymmetricKeyCertificate($storeId);
        $aesKey = random_bytes(32);
        $aesIv = random_bytes(16);

        $response = $this->httpClient->postJson(
            KsefEndpoints::SESSIONS_ONLINE,
            ['Authorization' => 'Bearer ' . $state->getAccessToken()],
            [
                'formCode' => [
                    'systemCode' => 'FA (3)',
                    'schemaVersion' => '1-0E',
                    'value' => 'FA',
                ],
                'encryption' => [
                    'encryptedSymmetricKey' => $this->crypto->encryptRsaOaepSha256Base64($aesKey, $certificatePem),
                    'initializationVector' => base64_encode($aesIv),
                ],
            ],
            $storeId
        );

        $referenceNumber = (string) ($response['referenceNumber'] ?? '');
        if ($referenceNumber === '') {
            throw new KsefApiException('KSeF session creation returned empty reference number.');
        }

        $state->setSessionReferenceNumber($referenceNumber);
        $state->setAesKey($aesKey);
        $state->setAesIv($aesIv);

        return $referenceNumber;
    }

    public function close(KsefState $state, ?int $storeId = null): void
    {
        $sessionReferenceNumber = $state->getSessionReferenceNumber();
        if (is_null($sessionReferenceNumber) || $sessionReferenceNumber === '') {
            return;
        }

        $this->httpClient->postJson(
            sprintf(KsefEndpoints::SESSIONS_ONLINE_CLOSE, $sessionReferenceNumber),
            ['Authorization' => 'Bearer ' . $state->getAccessToken()],
            [],
            $storeId
        );

        $state->setSessionReferenceNumber(null);
    }

    private function resolveSymmetricKeyCertificate(?int $storeId): string
    {
        $certificates = $this->httpClient->getJson(KsefEndpoints::SECURITY_PUBLIC_KEYS, [], $storeId);

        foreach ($certificates as $certificate) {
            $usage = $certificate['usage'] ?? [];
            if (!is_array($usage) || !in_array('SymmetricKeyEncryption', $usage, true)) {
                continue;
            }

            $certificateBase64 = (string) ($certificate['certificate'] ?? '');
            if ($certificateBase64 === '') {
                continue;
            }

            return $this->crypto->certificateBase64ToPem($certificateBase64);
        }

        throw new KsefApiException('Unable to resolve SymmetricKeyEncryption certificate.');
    }
}
