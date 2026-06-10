<?php

namespace Furgonetka\Pickups\Controller\Adminhtml\Integration;

class Connect extends \Magento\Backend\App\Action
{

    protected $_resultPageFactory;
    protected $furgonetkaApi;
    protected $helper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Furgonetka\Pickups\Helper\FurgonetkaApi $furgonetkaApi,
        \Furgonetka\Pickups\Helper\HelperFunctions $helper
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->furgonetkaApi = $furgonetkaApi;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
        try {
            $result = $this->helper->createOrUpdateAdminUser();
            $result = $this->furgonetkaApi->createIntegrationOAuthApplication($result['api_key']);

            if (empty($result['client_id'])) {
                throw new \Exception('Wystąpił błąd podczas połączenia. Spróbuj ponownie lub skontaktuj się z Furgonetka.pl.');
            }

            $authUrl = $this->furgonetkaApi->getOauthUrl($result['client_id']);
            $this->helper->clearCache(['config']);

            $this->_redirect($authUrl);
        } catch (\Exception $exception) {
            $this->messageManager->addError($exception->getMessage());
            $this->_redirect($this->_redirect->getRefererUrl());
        }

    }

}
