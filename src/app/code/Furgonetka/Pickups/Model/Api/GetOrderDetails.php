<?php

namespace Furgonetka\Pickups\Model\Api;

class GetOrderDetails extends \Magento\Framework\Model\AbstractModel implements \Furgonetka\Pickups\Api\GetOrderDetailsInterface
{


    protected $helper;


    /**
     * GetOrderDetails constructor.
     *
     * @param \Furgonetka\Pickups\Helper\HelperFunctions $helper
     */

    public function __construct(
        \Furgonetka\Pickups\Helper\HelperFunctions $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Pobiera zamówienia do SAP
     *
     * @param $id
     *
     * @return array
     *
     */
    public function getInfo($id)
    {
        $response = null;
        $orderDetails = $this->helper->getOrderDetails($id);
        $pointDetails = !empty($orderDetails['point']) ? $orderDetails['point'] : $orderDetails;

        if (!empty($pointDetails['code']) && !empty($pointDetails['type'])) {
            $response = [
                [
                    'receiver_point_code' => $orderDetails['code'],
                    'receiver_point_type' => $orderDetails['type']
                ],
            ];
        }

        return $response;
    }

}
