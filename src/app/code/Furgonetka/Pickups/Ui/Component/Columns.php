<?php

namespace Furgonetka\Pickups\Ui\Component;

class Columns extends \Magento\Ui\Component\Listing\Columns
{
    public function prepare()
    {
        $config = $this->getData('config');
        if (isset($config['childDefaults']['fieldAction'])) {
            unset($config['childDefaults']['fieldAction']);
        }
        $this->setData('config', (array)$config);
        parent::prepare();
    }
}