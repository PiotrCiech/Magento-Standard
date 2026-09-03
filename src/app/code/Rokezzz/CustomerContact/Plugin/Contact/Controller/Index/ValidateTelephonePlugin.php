<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Plugin\Contact\Controller\Index;

use Magento\Contact\Controller\Index\Post;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Rokezzz\CustomerContact\Model\PhoneValidator;

final class ValidateTelephonePlugin
{
    public function __construct(
        private readonly PhoneValidator $phoneValidator,
        private readonly MessageManager $messageManager,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly ResultFactory $resultFactory
    ) {
    }

    public function aroundExecute(Post $subject, callable $proceed): ResultInterface
    {
        $request = $subject->getRequest();

        if (!$request instanceof HttpRequest) {
            return $proceed();
        }

        if (!$request->isPost()) {
            return $proceed();
        }

        try {
            $normalizedPhone = $this->phoneValidator->normalizeOptional(
                (string) $request->getParam('telephone', '')
            );

            if ($normalizedPhone !== '') {
                $request->setParam('telephone', $normalizedPhone);
            }
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->dataPersistor->set('contact_us', $request->getParams());

            /** @var Redirect $redirect */
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            return $redirect->setPath('contact/index');
        }

        return $proceed();
    }
}
