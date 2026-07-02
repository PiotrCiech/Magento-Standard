<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Block\Contact;

use Magento\Framework\View\Element\Template;
use Rokezzz\CustomerContact\ViewModel\AssignedAdminDataProvider;

class AssignedAdmin extends Template
{
    public function __construct(
        Template\Context $context,
        private readonly AssignedAdminDataProvider $viewModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getAssignedAdminViewModel(): AssignedAdminDataProvider
    {
        return $this->viewModel;
    }
}
