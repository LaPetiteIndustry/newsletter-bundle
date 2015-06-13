<?php

namespace Lpi\NewsletterBundle\Integration;


class Access {

    private $apiSecret;
    private $apiKey;

    public function __construct($apiSecret, $apiKey){

        $this->apiSecret = $apiSecret;
        $this->apiKey = $apiKey;
    }

}