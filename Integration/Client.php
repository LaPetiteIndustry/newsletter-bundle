<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 20/04/15
 * Time: 11:10
 */

namespace Lpi\NewsletterBundle\Integration;

use GuzzleHttp\Client as GuzzleClient;

class Client extends GuzzleClient {

    public function __construct($args) {
        parent::__construct(['base_url' => 'https://api.mailjet.com/v3/REST/',
            'defaults' => [
                'auth'    => [$args['key'], $args['secret']]
            ]
        ]);

    }
} 