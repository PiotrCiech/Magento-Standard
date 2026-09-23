<?php
declare(strict_types=1);

namespace Rokezzz\ProductAdvance\Controller\Pdf;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

class Download implements HttpGetActionInterface
{
    public function __construct(
        private RequestInterface $request,
        private ProductRepositoryInterface $productRepository,
        private RawFactory $rawFactory,
        private PageFactory $pageFactory,
        private Registry $registry
    ) {
    }

    public function execute()
    {
        $productId = (int) $this->request->getParam('id');
        $resultRaw = $this->rawFactory->create();

        try {
            $product = $this->productRepository->getById($productId);
        } catch (\Exception $e) {
            $resultRaw->setContents('Produkt nie został odnaleziony.');

            return $resultRaw;
        }

        if (!$this->registry->registry('current_product')) {
            $this->registry->register('current_product', $product);
        }

        $page = $this->pageFactory->create();
        $block = $page->getLayout()
            ->createBlock(\Magento\Catalog\Block\Product\View::class)
            ->setProduct($product)
            ->setTemplate('Rokezzz_ProductAdvance::pdf/product_spec.phtml');

        $block->setData('product', $product);
        $html = $block->toHtml();
        $resultRaw->setHeader('Content-Type', 'text/html; charset=UTF-8');
        $resultRaw->setContents($html);

        return $resultRaw;
    }
}
