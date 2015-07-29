<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 29/07/2015
 * Time: 16:23
 */

namespace Lpi\NewsletterBundle\Integration\Mailchimp\Domain;


class Contact
{
    private $emailAdress;

    /**
     * Contact constructor.
     */
    public function __construct($emailAdress)
    {
        $this->emailAdress = $emailAdress;
    }

    /**
     * @return mixed
     */
    public function getEmailAdress()
    {
        return $this->emailAdress;
    }

    public function payload()
    {
        return json_encode(
            [
                'email_address' => $this->getEmailAdress(),
                'status' => 'subscribed'
            ]);
    }

}