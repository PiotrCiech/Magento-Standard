<?php

namespace Furgonetka\Pickups\Model\Config\Source;

class Testmode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Wybierz')],
            ['value' => 2, 'label' => __('Produkcyjne (https://furgonetka.pl/)')],
            ['value' => 1, 'label' => __('Sandbox (https://sandbox.furgonetka.pl/)')],
        ];
    }
}

