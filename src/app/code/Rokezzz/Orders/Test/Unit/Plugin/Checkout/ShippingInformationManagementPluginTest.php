<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Test\Unit\Plugin\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Orders\Model\Config;
use Rokezzz\Orders\Model\OrderCommentRequestReader;
use Rokezzz\Orders\Model\OrderCommentSaver;
use Rokezzz\Orders\Plugin\Checkout\ShippingInformationManagementPlugin;

final class ShippingInformationManagementPluginTest extends TestCase
{
    private Config&MockObject $config;

    private OrderCommentRequestReader&MockObject $orderCommentRequestReader;

    private OrderCommentSaver&MockObject $orderCommentSaver;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->orderCommentRequestReader = $this->createMock(OrderCommentRequestReader::class);
        $this->orderCommentSaver = $this->createMock(OrderCommentSaver::class);
    }

    public function testBeforeSaveAddressInformationSkipsWhenDisabled(): void
    {
        $this->config->method('isEnabled')->willReturn(false);
        $this->orderCommentSaver->expects($this->never())->method('save');

        $plugin = new ShippingInformationManagementPlugin(
            $this->config,
            $this->orderCommentRequestReader,
            $this->orderCommentSaver
        );
        $plugin->beforeSaveAddressInformation(
            $this->createStub(ShippingInformationManagement::class),
            15,
            $this->createStub(ShippingInformationInterface::class)
        );
    }

    public function testBeforeSaveAddressInformationSavesCommentWhenEnabled(): void
    {
        $addressInformation = $this->createMock(ShippingInformationInterface::class);

        $this->config->method('isEnabled')->willReturn(true);
        $this->orderCommentRequestReader->method('fromShippingInformation')
            ->with($addressInformation)
            ->willReturn('Leave at reception');
        $this->orderCommentSaver->expects($this->once())
            ->method('save')
            ->with(15, 'Leave at reception');

        $plugin = new ShippingInformationManagementPlugin(
            $this->config,
            $this->orderCommentRequestReader,
            $this->orderCommentSaver
        );
        $plugin->beforeSaveAddressInformation(
            $this->createStub(ShippingInformationManagement::class),
            15,
            $addressInformation
        );
    }
}
