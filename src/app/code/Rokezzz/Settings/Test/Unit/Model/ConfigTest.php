<?php

declare(strict_types=1);

namespace Rokezzz\Settings\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rokezzz\Settings\Model\Config;

final class ConfigTest extends TestCase
{
    private ScopeConfigInterface&Stub $scopeConfig;
    private EncryptorInterface&Stub $encryptor;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $this->encryptor = $this->createStub(EncryptorInterface::class);
    }

    public function testGetNipStripsNonDigits(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('123-456-32-18');

        $this->assertSame('1234563218', (new Config($this->scopeConfig, $this->encryptor))->getNip(1));
    }

    public function testGetCountryCodeFallsBackToPoland(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('  ');

        $this->assertSame('PL', (new Config($this->scopeConfig, $this->encryptor))->getCountryCode(1));
    }

    public function testGetKsefTokenDecryptsStoredValue(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('encrypted');
        $this->encryptor->method('decrypt')->with('encrypted')->willReturn('plain-token');

        $this->assertSame('plain-token', (new Config($this->scopeConfig, $this->encryptor))->getKsefToken(1));
    }

    public function testGetKsefNipFallsBackToCompanyNip(): void
    {
        $this->scopeConfig->method('getValue')->willReturnCallback(
            static function (string $path): string {
                return $path === 'rokezzz_settings/company/nip' ? '5252344408' : '';
            }
        );

        $this->assertSame('5252344408', (new Config($this->scopeConfig, $this->encryptor))->getKsefNip(1));
    }
}
