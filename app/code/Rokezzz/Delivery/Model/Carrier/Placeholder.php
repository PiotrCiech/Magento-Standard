<?php

declare(strict_types=1);

namespace Rokezzz\Delivery\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class Placeholder extends AbstractCarrier implements CarrierInterface
{
    protected $_code = 'rokezzz_empty';
    protected $_isFixed = true;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        private readonly ResultFactory $rateResultFactory,
        private readonly MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        if (isset($data['code'])) {
            $this->_code = (string) $data['code'];
        }
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var Result $result */
        $result = $this->rateResultFactory->create();
        $method = $this->rateMethodFactory->create();

        $price = (float) $this->getConfigData('price');

        $method->setCarrier($this->_code);
        $method->setCarrierTitle((string) $this->getConfigData('title'));
        $method->setMethod($this->_code);
        $method->setMethodTitle((string) $this->getConfigData('name'));
        $method->setPrice($price);
        $method->setCost($price);

        $result->append($method);

        return $result;
    }

    public function getAllowedMethods(): array
    {
        return [$this->_code => (string) $this->getConfigData('name')];
    }
}
