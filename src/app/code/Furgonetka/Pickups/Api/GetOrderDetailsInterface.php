<?php

namespace Furgonetka\Pickups\Api;

interface GetOrderDetailsInterface
{

    /**
     * Returns order details
     *
     * @param string $id order id
     *
     * @return string Greeting message with users name.
     * @api
     */
    public function getInfo($id);

}
