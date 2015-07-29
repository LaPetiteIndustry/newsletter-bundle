<?php
namespace Lpi\NewsletterBundle\Integration;

use GuzzleHttp\Client as GuzzleClient;
use Monolog\Logger;

abstract class ClientIntegration extends GuzzleClient
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct($baseUri, $key, $secret, Logger $logger)
    {
        parent::__construct(['base_uri' => $baseUri, 'auth' => [$key, $secret]]);
        $this->logger = $logger;
    }

    protected function getLogger() {
        return $this->logger;
    }
}