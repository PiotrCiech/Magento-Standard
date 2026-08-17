<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Ksef;

final class KsefState
{
    private ?string $sessionReferenceNumber = null;
    private ?string $authenticationToken = null;
    private ?string $authReferenceNumber = null;
    private ?string $accessToken = null;
    private ?string $refreshToken = null;
    private ?string $aesKey = null;
    private ?string $aesIv = null;

    public function getSessionReferenceNumber(): ?string
    {
        return $this->sessionReferenceNumber;
    }

    public function setSessionReferenceNumber(?string $sessionReferenceNumber): void
    {
        $this->sessionReferenceNumber = $sessionReferenceNumber;
    }

    public function getAuthenticationToken(): ?string
    {
        return $this->authenticationToken;
    }

    public function setAuthenticationToken(?string $authenticationToken): void
    {
        $this->authenticationToken = $authenticationToken;
    }

    public function getAuthReferenceNumber(): ?string
    {
        return $this->authReferenceNumber;
    }

    public function setAuthReferenceNumber(?string $authReferenceNumber): void
    {
        $this->authReferenceNumber = $authReferenceNumber;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getAesKey(): ?string
    {
        return $this->aesKey;
    }

    public function setAesKey(?string $aesKey): void
    {
        $this->aesKey = $aesKey;
    }

    public function getAesIv(): ?string
    {
        return $this->aesIv;
    }

    public function setAesIv(?string $aesIv): void
    {
        $this->aesIv = $aesIv;
    }
}
