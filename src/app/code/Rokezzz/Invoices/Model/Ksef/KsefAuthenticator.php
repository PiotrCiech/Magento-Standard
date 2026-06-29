<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use Magento\Framework\Serialize\Serializer\Json;
use Rokezzz\Invoices\Exception\KsefApiException;

class KsefAuthenticator
{
    private const MAX_AUTH_POLL_ATTEMPTS = 60;
    private const AUTH_POLL_INTERVAL_MICROSECONDS = 500_000;

    public function __construct(
        private readonly KsefHttpClient $httpClient,
        private readonly KsefConfigProvider $configProvider,
        private readonly KsefCrypto $crypto,
        private readonly Json $json
    ) {
    }

    public function authenticate(KsefState $state, ?int $storeId = null): void
    {
        $challengeData = $this->httpClient->postJson(
            KsefEndpoints::AUTH_CHALLENGE,
            [],
            [],
            $storeId
        );

        $challenge = (string) ($challengeData['challenge'] ?? '');
        $timestampMs = (string) ($challengeData['timestampMs'] ?? '');

        $publicKeyPem = $this->resolveTokenEncryptionCertificate($storeId);
        $tokenPayload = $this->configProvider->getToken($storeId) . '|' . $timestampMs;
        $encryptedToken = $this->crypto->encryptRsaOaepSha256Base64($tokenPayload, $publicKeyPem);

        $authStart = $this->httpClient->postJson(
            KsefEndpoints::AUTH_KSEF_TOKEN,
            [],
            [
                'encryptedToken' => $encryptedToken,
                'challenge' => $challenge,
                'contextIdentifier' => [
                    'type' => 'Nip',
                    'value' => $this->configProvider->getNip($storeId),
                ],
            ],
            $storeId
        );

        $state->setAuthenticationToken((string) ($authStart['authenticationToken']['token'] ?? ''));
        $state->setAuthReferenceNumber((string) ($authStart['referenceNumber'] ?? ''));

        $this->pollAuthStatus($state, $storeId);
        $this->redeemAccessToken($state, $storeId);
    }

    private function pollAuthStatus(KsefState $state, ?int $storeId): void
    {
        $authReferenceNumber = (string) $state->getAuthReferenceNumber();
        $authenticationToken = (string) $state->getAuthenticationToken();

        for ($attempt = 1; $attempt <= self::MAX_AUTH_POLL_ATTEMPTS; $attempt++) {
            $response = $this->httpClient->getJson(
                sprintf(KsefEndpoints::AUTH_STATUS, $authReferenceNumber),
                ['Authorization' => 'Bearer ' . $authenticationToken],
                $storeId
            );

            $status = $response['status'] ?? null;
            $statusCode = is_array($status) ? (int) ($status['code'] ?? 0) : 0;

            if ($statusCode === 200) {
                return;
            }

            if ($statusCode >= 400) {
                throw new KsefApiException(
                    'KSeF authentication failed: ' . $this->json->serialize($response)
                );
            }

            usleep(self::AUTH_POLL_INTERVAL_MICROSECONDS);
        }

        throw new KsefApiException('KSeF authentication timed out.');
    }

    private function redeemAccessToken(KsefState $state, ?int $storeId): void
    {
        $response = $this->httpClient->postJson(
            KsefEndpoints::AUTH_TOKEN_REDEEM,
            ['Authorization' => 'Bearer ' . $state->getAuthenticationToken()],
            ['referenceNumber' => $state->getAuthReferenceNumber()],
            $storeId
        );

        $state->setAccessToken((string) (($response['accessToken']['token'] ?? $response['accessToken']) ?? ''));
        $state->setRefreshToken((string) (($response['refreshToken']['token'] ?? $response['refreshToken']) ?? ''));
    }

    private function resolveTokenEncryptionCertificate(?int $storeId): string
    {
        $certificates = $this->httpClient->getJson(KsefEndpoints::SECURITY_PUBLIC_KEYS, [], $storeId);

        foreach ($certificates as $certificate) {
            $usage = $certificate['usage'] ?? [];
            if (!is_array($usage) || !in_array('KsefTokenEncryption', $usage, true)) {
                continue;
            }

            $certificateBase64 = (string) ($certificate['certificate'] ?? '');
            if ($certificateBase64 === '') {
                continue;
            }

            return $this->crypto->certificateBase64ToPem($certificateBase64);
        }

        throw new KsefApiException('Unable to resolve KsefTokenEncryption certificate.');
    }
}
