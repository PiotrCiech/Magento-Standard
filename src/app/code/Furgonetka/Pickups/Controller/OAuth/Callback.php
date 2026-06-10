<?php

namespace Furgonetka\Pickups\Controller\OAuth;

class Callback extends \Magento\Framework\App\Action\Action
{

    protected $apiHelper;
    protected $HelperBackend;
    protected $messageManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Furgonetka\Pickups\Helper\FurgonetkaApi $apiHelper,
        \Magento\Backend\Helper\Data $HelperBackend,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->apiHelper = $apiHelper;
        $this->HelperBackend = $HelperBackend;
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }


    public function execute()
    {
        $code = $this->getRequest()->getParam('code');
        if ($code) {
            try {
                $result = $this->apiHelper->createTokenByOauth($code);
                if ($result !== false) {
                    $sourceResult = $this->apiHelper->addIntegrationSource($result);
                    if (!empty($sourceResult['sourceId'])) {
                        header('Location: ' . $this->HelperBackend->getHomePageUrl(), true, 301);
                        die();
                    } else {
                        echo __("Wystąpił błąd podczas dodawania źródła sprzedaży. Spróbuj ponownie lub skontaktuj się z Furgonetka.pl.") . "<br/>";
                    }
                } else {
                    echo __("Wystąpił błąd podczas połączenia. Spróbuj ponownie lub skontaktuj się z Furgonetka.pl.") . "<br/>";
                }
            } catch (\Exception $exception) {
                echo "Wystąpił nieznany błąd. Spróbuj ponownie lub skontaktuj się z Furgonetka.pl.<br/>";
            }

        }

        echo '<br/><a href="' . $this->HelperBackend->getHomePageUrl() . '">Powrót do sklepu</a>';
    }
}