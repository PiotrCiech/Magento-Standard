<?php

namespace Furgonetka\Pickups\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Fedex extends Inpost
{

    protected $_code = 'f_fedex';

}