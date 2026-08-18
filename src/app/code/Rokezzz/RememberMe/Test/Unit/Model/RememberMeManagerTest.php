<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\RememberMe\Model\AdminRememberMeFlag;
use Rokezzz\RememberMe\Model\Config;
use Rokezzz\RememberMe\Model\RememberMeManager;

final class RememberMeManagerTest extends TestCase
{
    private RememberMeManager $manager;

    protected function setUp(): void
    {
        $this->manager = new RememberMeManager(
            new Config($this->createStub(ScopeConfigInterface::class)),
            $this->createStub(CookieManagerInterface::class),
            $this->createStub(CookieMetadataFactory::class),
            new AdminRememberMeFlag()
        );
    }

    public function testIsRememberMeCheckedForCustomerReadsRequestParam(): void
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->with('remember_me')->willReturn('1');

        $this->assertTrue($this->manager->isRememberMeCheckedForCustomer($request));
    }

    public function testIsRememberMeCheckedForAdminUsesCapturedFlag(): void
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->with('login')->willReturn(['remember_me' => '1']);
        $flag = new AdminRememberMeFlag();
        $flag->captureFromRequest($request);

        $manager = new RememberMeManager(
            new Config($this->createStub(ScopeConfigInterface::class)),
            $this->createStub(CookieManagerInterface::class),
            $this->createStub(CookieMetadataFactory::class),
            $flag
        );

        $this->assertTrue($manager->isRememberMeCheckedForAdmin($request));
    }

    public function testIsRememberMeCheckedForAdminReadsLoginPayload(): void
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->with('login')->willReturn(['remember_me' => '1']);

        $this->assertTrue($this->manager->isRememberMeCheckedForAdmin($request));
    }

    public function testIsRememberMeCheckedForAdminReturnsFalseWithoutPayload(): void
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->with('login')->willReturn('admin');

        $this->assertFalse($this->manager->isRememberMeCheckedForAdmin($request));
    }
}
