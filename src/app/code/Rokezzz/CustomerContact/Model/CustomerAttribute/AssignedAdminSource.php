<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Model\CustomerAttribute;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\User\Model\ResourceModel\User\CollectionFactory;

class AssignedAdminSource extends AbstractSource
{
    public function __construct(
        private readonly CollectionFactory $userCollectionFactory
    ) {
    }

    public function getAllOptions(): array
    {
        if (!is_null($this->_options)) {
            return $this->_options;
        }

        $options = [
            [
                'label' => __('-- Please Select --'),
                'value' => '',
            ],
        ];

        $collection = $this->userCollectionFactory->create();
        $collection->addFieldToFilter('main_table.is_active', 1);
        $collection->setOrder('firstname', 'ASC');
        $collection->setOrder('lastname', 'ASC');

        foreach ($collection as $user) {
            $label = trim(sprintf(
                '%s %s <%s>',
                (string) $user->getFirstName(),
                (string) $user->getLastName(),
                (string) $user->getEmail()
            ));

            $options[] = [
                'label' => $label,
                'value' => (int) $user->getId(),
            ];
        }

        $this->_options = $options;

        return $this->_options;
    }
}
