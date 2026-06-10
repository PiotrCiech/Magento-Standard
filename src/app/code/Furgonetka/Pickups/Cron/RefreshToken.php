<?php

namespace Furgonetka\Pickups\Cron;

class RefreshToken
{
    private $apiHelper;

    public function __construct(
        \Furgonetka\Pickups\Helper\FurgonetkaApi $apiHelper
    ) {
        $this->apiHelper = $apiHelper;
    }


    public function execute()
    {
        $this->apiHelper->refreshToken();
        return $this;
    }
}