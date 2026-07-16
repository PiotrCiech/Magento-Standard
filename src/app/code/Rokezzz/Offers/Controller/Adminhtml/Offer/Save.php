<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Controller\Adminhtml\Offer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Offers\Api\Data\OfferItemDataInterface;
use Rokezzz\Offers\Api\OfferManagementInterface;
use Rokezzz\Offers\Controller\Adminhtml\Offer as OfferAction;
use Rokezzz\Offers\Model\Data\OfferItemDataCreator;

class Save extends OfferAction implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private readonly OfferManagementInterface $offerManagement,
        private readonly OfferItemDataCreator $offerItemDataCreator
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();

        if (!$request instanceof HttpRequest) {
            return $resultRedirect->setPath('*/*/');
        }

        $data = $this->extractFormData($request->getPostValue());

        if ($data === []) {
            $this->messageManager->addErrorMessage(
                __('Unable to save the offer. The form data is missing. Please refresh the page and try again.')
            );

            return $resultRedirect->setPath('*/*/');
        }

        $offerId = (int) ($data['entity_id'] ?? 0);
        $requestId = (int) ($data['request_id'] ?? 0);
        $adminNote = trim((string) ($data['admin_note'] ?? ''));
        $expiresAt = $this->normalizeDateTime((string) ($data['expires_at'] ?? ''));
        $items = $this->buildOfferItems($data['items'] ?? []);
        $redirectBack = (string) ($data['back'] ?? $request->getParam('back'));

        try {
            if ($offerId > 0) {
                $offer = $this->offerManagement->update($offerId, $adminNote, $expiresAt, $items);
            } elseif ($requestId > 0) {
                $offer = $this->offerManagement->createFromRequest($requestId, $adminNote, $expiresAt, $items);
            } else {
                throw new LocalizedException(__('Unable to save the offer.'));
            }

            $this->messageManager->addSuccessMessage(__('The offer has been saved.'));

            if ($redirectBack === 'edit') {
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $offer->getEntityId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('The offer could not be saved right now. Please try again later.')
            );
        }

        if ($offerId > 0) {
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $offerId]);
        }

        return $resultRedirect->setPath('*/*/edit', ['request_id' => $requestId]);
    }

    /**
     * Magento UI forms submit fields at the root of POST (same as CMS),
     * optionally nested under "data" depending on client version/config.
     *
     * @param array<string, mixed>|null $postData
     *
     * @return array<string, mixed>
     */
    private function extractFormData(mixed $postData): array
    {
        if (!is_array($postData) || $postData === []) {
            return [];
        }

        if (isset($postData['data']) && is_array($postData['data'])) {
            $data = $postData['data'];
        } else {
            $data = $postData;
        }

        unset($data['form_key']);

        if (!isset($data['request_id']) && !isset($data['entity_id']) && !isset($data['items'])) {
            return [];
        }

        return $data;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     *
     * @return OfferItemDataInterface[]
     */
    private function buildOfferItems(array $rows): array
    {
        $items = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $itemData = $this->offerItemDataCreator->create();
            $requestItemId = $row['request_item_id'] ?? null;
            $itemData->setRequestItemId(
                is_null($requestItemId) || $requestItemId === '' ? null : (int) $requestItemId
            );
            $itemData->setProductId((int) ($row['product_id'] ?? 0));
            $itemData->setOfferedQty((float) ($row['offered_qty'] ?? 0));
            $itemData->setOfferedPrice((float) ($row['offered_price'] ?? 0));
            $items[] = $itemData;
        }

        return $items;
    }

    private function normalizeDateTime(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return $value;
    }
}
