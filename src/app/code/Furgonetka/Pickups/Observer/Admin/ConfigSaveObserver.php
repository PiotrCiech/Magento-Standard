<?php

namespace Furgonetka\Pickups\Observer\Admin;

use Furgonetka\Pickups\Helper\HelperFunctions;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ConfigSaveObserver implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var HelperFunctions
     */
    private $helperFunctions;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * ConfigChange constructor.
     *
     * @param RequestInterface $request
     * @param WriterInterface  $configWriter
     */
    public function __construct(
        RequestInterface $request,
        WriterInterface $configWriter,
        HelperFunctions $helperFunctions,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->configWriter = $configWriter;
        $this->helperFunctions = $helperFunctions;
        $this->messageManager = $messageManager;
    }

    public function execute(EventObserver $observer)
    {
        $changedPaths = $observer->getEvent()->getChangedPaths();

        if (\in_array(\Furgonetka\Pickups\Helper\Data::IS_TEST_MODE, $changedPaths)) {
            try {
                $this->helperFunctions->deleteConfig(\Furgonetka\Pickups\Helper\Data::API_CLIENT_ID);
                $this->helperFunctions->deleteConfig(\Furgonetka\Pickups\Helper\Data::API_CLIENT_SECRET);
                $this->helperFunctions->deleteConfig(\Furgonetka\Pickups\Helper\Data::ACCESS_TOKEN);
                $this->helperFunctions->deleteConfig(\Furgonetka\Pickups\Helper\Data::REFRESH_TOKEN);
                $this->helperFunctions->deleteConfig(\Furgonetka\Pickups\Helper\Data::SOURCE_ID);
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Wystąpił błąd podczas konfiguracji środowiska. Spróbuj ponownie lub skontaktuj się z Furgonetka.pl.'));
            }
        }

        $this->helperFunctions->clearCache(['config']);
        return $this;
    }
}
