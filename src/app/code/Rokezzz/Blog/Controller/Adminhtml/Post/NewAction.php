<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class NewAction extends Action
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::posts';

    public function execute(): ResultInterface
    {
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        return $resultForward->forward('edit');
    }
}